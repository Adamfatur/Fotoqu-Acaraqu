<?php

namespace App\Services;

use App\Models\Photo;
use App\Models\PhotoSession;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoService
{
    /**
     * Store a photo from photobox camera.
     */
    public function storePhoto(PhotoSession $session, UploadedFile $file, int $sequenceNumber): Photo
    {
        // Generate unique filename
        $filename = $this->generatePhotoFilename($session, $sequenceNumber);
        $localDir = storage_path("app/private/photobox/{$session->session_code}");
        $localPath = $localDir . '/' . $filename;

        // Ensure local directory exists
        if (!is_dir($localDir)) {
            @mkdir($localDir, 0755, true);
        }

        \Log::info("PhotoService: Saving photo locally (defer S3)", [
            'session_code' => $session->session_code,
            'filename' => $filename,
            'local_path' => $localPath,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType()
        ]);

        try {
            // Move uploaded file to local private storage
            $moved = move_uploaded_file($file->getRealPath(), $localPath);
            if (!$moved) {
                // Fallback to stream copy
                $moved = copy($file->getRealPath(), $localPath);
            }
            if (!$moved || !file_exists($localPath)) {
                throw new \Exception("Failed to save photo locally: {$localPath}");
            }

        } catch (\Exception $e) {
            \Log::error("PhotoService: Local save exception", [
                'local_path' => $localPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Create photo record
        $photo = Photo::create([
            'photo_session_id' => $session->id,
            'sequence_number' => $sequenceNumber,
            'filename' => $filename,
            's3_path' => '',
            'local_path' => $localPath,
            'file_size' => $file->getSize(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'dimensions' => $this->getImageDimensions($file),
                'storage' => 'local',
            ],
        ]);

        return $photo;
    }

    /**
     * Store multiple photos from photobox session.
     */
    public function storeSessionPhotos(PhotoSession $session, array $files): array
    {
        $photos = [];
        
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $photos[] = $this->storePhoto($session, $file, $index + 1);
            }
        }

        return $photos;
    }

    /**
     * Mark photos as selected for frame creation.
     */
    public function selectPhotosForFrame(PhotoSession $session, array $photoIds): void
    {
        // First, unselect all photos in this session
        $session->photos()->update(['is_selected' => false]);

        // Then select the specified photos
        Photo::whereIn('id', $photoIds)
            ->where('photo_session_id', $session->id)
            ->update(['is_selected' => true]);

        // Validate selection count matches frame slots
        $selectedCount = $session->selectedPhotos()->count();
        $requiredSlots = (int) $session->frame_slots;
        if ($selectedCount !== $requiredSlots) {
            throw new \Exception("Selected {$selectedCount} photos but frame requires {$requiredSlots} photos");
        }
    }

    /**
     * Get all photos for a session with URLs.
     */
    public function getSessionPhotosWithUrls(PhotoSession $session, int $expiresInMinutes = 60): array
    {
        return $session->photos()
            ->orderBy('sequence_number')
            ->get()
            ->map(function ($photo) use ($expiresInMinutes) {
                // Generate Laravel URL for serving photos (reliable alternative to S3 direct access)
                $laravelUrl = route('photobox.serve-photo', ['photo' => $photo->id]);
                
                // Also include S3 URLs for fallback/admin use
                $signedUrl = $photo->s3_path ? Storage::disk('s3')->temporaryUrl($photo->s3_path, now()->addMinutes($expiresInMinutes)) : null;
                $publicUrl = $photo->s3_path ? Storage::disk('s3')->url($photo->s3_path) : null;

                return [
                    'id' => $photo->id,
                    'sequence_number' => $photo->sequence_number,
                    'is_selected' => $photo->is_selected,
                    'filename' => $photo->filename,
                    's3_path' => $photo->s3_path,
                    'url' => $laravelUrl, // Primary URL for photobox interface
                    'signed_url' => $signedUrl, // Fallback S3 signed URL
                    'public_url' => $publicUrl, // Fallback S3 public URL
                    'local' => !empty($photo->local_path),
                ];
            })
            ->toArray();
    }

    /**
     * Simulate taking photos for demo/testing purposes.
     */
    public function simulatePhotoCapture(PhotoSession $session): array
    {
    $photos = [];
    $totalPhotos = config('fotoku.total_photos', 3);

        for ($i = 1; $i <= $totalPhotos; $i++) {
            // Create a simple colored image for simulation
            $image = imagecreate(800, 600);
            $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagefill($image, 0, 0, $color);
            
            // Add sequence number to image
            $textColor = imagecolorallocate($image, 255, 255, 255);
            imagestring($image, 5, 350, 280, "Photo #{$i}", $textColor);

            // Save to temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'photo_') . '.jpg';
            imagejpeg($image, $tempFile, 100); // Maximum quality for best results
            imagedestroy($image);

            // Create UploadedFile instance
            $uploadedFile = new UploadedFile(
                $tempFile,
                "photo_{$i}.jpg",
                'image/jpeg',
                null,
                true
            );

            // Store photo
            $photos[] = $this->storePhoto($session, $uploadedFile, $i);

            // Clean up temp file
            unlink($tempFile);
        }

        return $photos;
    }

    /**
     * Store a photo from base64 data (for photobox camera).
     */
    public function savePhotoFromBase64(PhotoSession $session, string $base64Data, int $sequenceNumber): Photo
    {
        // Remove data:image/... prefix if present
        if (strpos($base64Data, 'data:') === 0) {
            $base64Data = explode(',', $base64Data)[1];
        }

        // Decode base64 data
        $imageData = base64_decode($base64Data);
        
        if ($imageData === false) {
            throw new \Exception('Invalid base64 image data');
        }

        // Generate unique filename
        $filename = $this->generatePhotoFilename($session, $sequenceNumber);
    $localDir = storage_path("app/private/photobox/{$session->session_code}");
    $localPath = $localDir . '/' . $filename;

        \Log::info("PhotoService: Saving base64 photo locally (defer S3)", [
            'session_code' => $session->session_code,
            'sequence_number' => $sequenceNumber,
            'filename' => $filename,
            'local_path' => $localPath,
            'data_size' => strlen($imageData)
        ]);

        try {
            // Ensure local directory exists
            if (!is_dir($localDir)) {
                @mkdir($localDir, 0755, true);
            }
            // Save binary to local path
            $bytes = file_put_contents($localPath, $imageData);
            if ($bytes === false || !file_exists($localPath)) {
                throw new \Exception("Failed to save base64 photo locally: {$localPath}");
            }

        } catch (\Exception $e) {
            \Log::error("PhotoService: Base64 local save exception", [
                'local_path' => $localPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Get image dimensions from binary data
        $dimensions = $this->getImageDimensionsFromData($imageData);

        // Create photo record only after successful S3 upload
        $photo = Photo::create([
            'photo_session_id' => $session->id,
            'sequence_number' => $sequenceNumber,
            'filename' => $filename,
            's3_path' => '',
            'local_path' => $localPath,
            'metadata' => [
                'source' => 'photobox_capture',
                'size' => strlen($imageData),
                'mime_type' => 'image/jpeg',
                'dimensions' => $dimensions,
                'captured_at' => now()->toISOString(),
                'storage' => 'local',
            ],
        ]);

        return $photo;
    }

    /**
     * Upload all locally stored photos for a session to S3 and update records.
     * Keeps local files until confirmed uploaded, then removes them.
     */
    public function uploadSessionPhotosToS3(PhotoSession $session): array
    {
        $uploaded = [];
        foreach ($session->photos as $photo) {
            if ($photo->local_path && file_exists($photo->local_path)) {
                $s3Path = "photos/{$session->session_code}/{$photo->filename}";
                try {
                    $data = file_get_contents($photo->local_path);
                    $ok = Storage::disk('s3')->put($s3Path, $data);
                    if ($ok) {
                        // Verify and then update
                        if (Storage::disk('s3')->exists($s3Path)) {
                            $photo->update([
                                's3_path' => $s3Path,
                                's3_url' => Storage::disk('s3')->url($s3Path),
                                'uploaded_at' => now(),
                            ]);
                            // Remove local only after successful S3
                            @unlink($photo->local_path);
                            $photo->local_path = null;
                            $photo->save();
                            $uploaded[] = $photo->id;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to upload photo to S3', [
                        'photo_id' => $photo->id,
                        's3_path' => $s3Path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        return $uploaded;
    }

    /**
     * Generate unique photo filename.
     */
    private function generatePhotoFilename(PhotoSession $session, int $sequenceNumber): string
    {
        $timestamp = now()->format('YmdHis');
        $random = Str::random(6);
        return "{$session->session_code}_photo_{$sequenceNumber}_{$timestamp}_{$random}.jpg";
    }

    /**
     * Get image dimensions from uploaded file.
     */
    private function getImageDimensions(UploadedFile $file): ?array
    {
        try {
            [$width, $height] = getimagesize($file->getRealPath());
            return ['width' => $width, 'height' => $height];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get image dimensions from binary data.
     */
    private function getImageDimensionsFromData(string $imageData): ?array
    {
        try {
            // Use in-memory dimension extraction to avoid disk I/O
            if (!function_exists('getimagesizefromstring')) {
                // Fallback to temp file if function is unavailable (older PHP)
                $tempFile = tempnam(sys_get_temp_dir(), 'image_') . '.jpg';
                file_put_contents($tempFile, $imageData);
                [$width, $height] = getimagesize($tempFile);
                @unlink($tempFile);
            } else {
                [$width, $height] = getimagesizefromstring($imageData);
            }

            return ['width' => $width, 'height' => $height];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Clean up old photos from S3 (for maintenance).
     */
    public function cleanupOldPhotos(int $daysOld = 90): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $oldPhotos = Photo::whereHas('photoSession', function ($query) use ($cutoffDate) {
            $query->where('created_at', '<', $cutoffDate);
        })->get();

        $deletedCount = 0;
        foreach ($oldPhotos as $photo) {
            try {
                Storage::disk('s3')->delete($photo->s3_path);
                $photo->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                // Log error but continue
                logger()->error("Failed to delete photo {$photo->id}: {$e->getMessage()}");
            }
        }

        return $deletedCount;
    }
}
