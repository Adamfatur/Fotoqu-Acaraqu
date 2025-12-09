<?php

use App\Models\Frame;
use App\Models\Photo;
use App\Models\PhotoSession;
use App\Models\Photobox;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('purges only the targeted session assets (DB, S3, and local)', function () {
    Storage::fake('s3');

    // Create users
    $admin = User::factory()->create();
    $customerA = User::factory()->create();
    $customerB = User::factory()->create();

    // Create photobox
    $photobox = Photobox::create([
        'code' => 'BOX-01',
        'name' => 'Photobox 01',
        'status' => 'active',
    ]);

    // Helper to create a session with one photo and a frame
    $makeSession = function (string $code, User $customer) use ($photobox) {
        $session = PhotoSession::create([
            'session_code' => $code,
            'photobox_id' => $photobox->id,
            'user_id' => $customer->id,
            'admin_id' => $customer->id, // not used in this test logic
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'frame_slots' => '6',
            'total_price' => 1000,
            'payment_status' => 'paid',
            'session_status' => 'cancelled', // eligible for purge
        ]);

        // Create S3 objects and local file for a photo
        $photoFilename = "{$code}_photo_1.jpg";
        $photoS3Path = "photos/{$code}/{$photoFilename}";
        Storage::disk('s3')->put($photoS3Path, 'photo-bytes');

        $localDir = storage_path("app/private/photobox/{$code}");
        if (!is_dir($localDir)) { mkdir($localDir, 0777, true); }
        $localPath = $localDir . "/{$photoFilename}";
        file_put_contents($localPath, 'local-photo');

        $photo = Photo::create([
            'photo_session_id' => $session->id,
            'sequence_number' => 1,
            'filename' => $photoFilename,
            'local_path' => $localPath,
            's3_path' => $photoS3Path,
            's3_url' => Storage::disk('s3')->url($photoS3Path),
        ]);

        // Create S3 object for a frame
        $frameFilename = "{$code}_frame.jpg";
        $frameS3Path = "frames/{$code}/{$frameFilename}";
        Storage::disk('s3')->put($frameS3Path, 'frame-bytes');

        $frame = Frame::create([
            'photo_session_id' => $session->id,
            'filename' => $frameFilename,
            's3_path' => $frameS3Path,
            's3_url' => Storage::disk('s3')->url($frameS3Path),
            'status' => 'completed',
        ]);

        return [$session, $photo, $frame, $localPath, $photoS3Path, $frameS3Path, $localDir];
    };

    // Create two sessions A and B
    [$sessionA, $photoA, $frameA, $localA, $s3photoA, $s3frameA, $dirA] = $makeSession('FOTOKU-TESTA', $customerA);
    [$sessionB, $photoB, $frameB, $localB, $s3photoB, $s3frameB, $dirB] = $makeSession('FOTOKU-TESTB', $customerB);

    // Sanity pre-asserts
    expect(file_exists($localA))->toBeTrue();
    expect(file_exists($localB))->toBeTrue();
    Storage::disk('s3')->assertExists($s3photoA);
    Storage::disk('s3')->assertExists($s3photoB);
    Storage::disk('s3')->assertExists($s3frameA);
    Storage::disk('s3')->assertExists($s3frameB);

    // Act as admin and purge Session A
    actingAs($admin);
    post(route('admin.sessions.purge-assets', $sessionA))->assertRedirect();

    // Assert Session A assets are removed
    expect(Photo::where('photo_session_id', $sessionA->id)->count())->toBe(0);
    expect(Frame::where('photo_session_id', $sessionA->id)->count())->toBe(0);
    Storage::disk('s3')->assertMissing($s3photoA);
    Storage::disk('s3')->assertMissing($s3frameA);
    expect(file_exists($localA))->toBeFalse();
    // Dir A should be cleaned up
    expect(is_dir($dirA))->toBeFalse();

    // Assert Session B remains intact
    expect(Photo::where('photo_session_id', $sessionB->id)->count())->toBe(1);
    expect(Frame::where('photo_session_id', $sessionB->id)->count())->toBe(1);
    Storage::disk('s3')->assertExists($s3photoB);
    Storage::disk('s3')->assertExists($s3frameB);
    expect(file_exists($localB))->toBeTrue();
});
