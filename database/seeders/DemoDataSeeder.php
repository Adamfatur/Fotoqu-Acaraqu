<?php

namespace Database\Seeders;

use App\Models\PhotoSession;
use App\Models\Photo;
use App\Models\Frame;
use App\Models\PaymentLog;
use App\Models\ActivityLog;
use App\Models\Photobox;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Get admin user
        $admin = User::where('email', 'admin@fotoku.com')->first();
        $photoboxes = Photobox::all();

        if (!$admin || $photoboxes->isEmpty()) {
            $this->command->error('Admin user or photoboxes not found. Run AdminSeeder and PhotoboxSeeder first.');
            return;
        }

        // Create demo customers
        $customers = [];
        $customerData = [
            ['name' => 'Andi Wijaya', 'email' => 'andi.wijaya@email.com'],
            ['name' => 'Sari Melati', 'email' => 'sari.melati@email.com'],
            ['name' => 'Budi Santoso', 'email' => 'budi.santoso@email.com'],
            ['name' => 'Linda Cahaya', 'email' => 'linda.cahaya@email.com'],
            ['name' => 'Rudi Hermawan', 'email' => 'rudi.hermawan@email.com'],
        ];

        foreach ($customerData as $data) {
            $customers[] = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => bcrypt('password')]
            );
        }

        // Create demo sessions for the last 7 days
        $sessions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sessionsCount = rand(2, 5); // 2-5 sessions per day

            for ($j = 0; $j < $sessionsCount; $j++) {
                $customer = $customers[array_rand($customers)];
                $photobox = $photoboxes[array_rand($photoboxes->toArray())];
                $frameSlots = [4, 6, 8][array_rand([4, 6, 8])];
                $price = match($frameSlots) {
                    4 => 25000,
                    6 => 35000,
                    8 => 45000,
                };

                $statuses = ['created', 'approved', 'in_progress', 'completed'];
                $weights = [10, 15, 10, 65]; // 65% completed, 15% approved, etc.
                $status = $this->weightedRandom($statuses, $weights);

                $session = PhotoSession::create([
                    'session_code' => PhotoSession::generateSessionCode(),
                    'photobox_id' => $photobox->id,
                    'user_id' => $customer->id,
                    'admin_id' => $admin->id,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'frame_slots' => $frameSlots,
                    'total_price' => $price,
                    'session_status' => $status,
                    'payment_status' => in_array($status, ['approved', 'in_progress', 'completed']) ? 'paid' : 'pending',
                    'created_at' => $date->copy()->addHours(rand(8, 18))->addMinutes(rand(0, 59)),
                    'approved_at' => in_array($status, ['approved', 'in_progress', 'completed']) ? $date->copy()->addHours(rand(8, 18)) : null,
                    'started_at' => in_array($status, ['in_progress', 'completed']) ? $date->copy()->addHours(rand(9, 19)) : null,
                    'completed_at' => $status === 'completed' ? $date->copy()->addHours(rand(10, 20)) : null,
                ]);

                $sessions[] = $session;

                // Create payment log if paid
                if ($session->payment_status === 'paid') {
                    $paymentMethod = ['cash', 'qris', 'edc'][array_rand(['cash', 'qris', 'edc'])];
                    PaymentLog::create([
                        'photo_session_id' => $session->id,
                        'admin_id' => $admin->id,
                        'amount' => $session->total_price,
                        'payment_method' => $paymentMethod,
                        'status' => 'success',
                        'notes' => 'Demo payment - ' . ucfirst($paymentMethod),
                        'created_at' => $session->created_at,
                    ]);
                }

                // Create photos for in_progress and completed sessions
                if (in_array($status, ['in_progress', 'completed'])) {
                    $photoCount = $status === 'completed' ? 10 : rand(3, 8);
                    
                    for ($k = 1; $k <= $photoCount; $k++) {
                        Photo::create([
                            'photo_session_id' => $session->id,
                            'photo_number' => $k,
                            'file_path' => "photos/{$session->session_code}/photo_{$k}.jpg",
                            's3_key' => "sessions/{$session->session_code}/photos/photo_{$k}.jpg",
                            's3_url' => "https://fotoku-photos.s3.amazonaws.com/sessions/{$session->session_code}/photos/photo_{$k}.jpg",
                            'is_selected' => $status === 'completed' && $k <= $frameSlots,
                            'metadata' => json_encode([
                                'camera_settings' => [
                                    'iso' => rand(100, 800),
                                    'aperture' => 'f/2.8',
                                    'shutter_speed' => '1/60',
                                    'flash' => true
                                ],
                                'file_size' => rand(2, 5) . 'MB',
                                'resolution' => '1920x1080'
                            ]),
                            'created_at' => $session->started_at ? $session->started_at->copy()->addMinutes($k * 2) : now(),
                        ]);
                    }
                }

                // Create frame for completed sessions
                if ($status === 'completed') {
                    Frame::create([
                        'photo_session_id' => $session->id,
                        'layout_type' => "{$frameSlots}_slots",
                        'file_path' => "frames/{$session->session_code}/frame.jpg",
                        's3_key' => "sessions/{$session->session_code}/frame/final_frame.jpg",
                        's3_url' => "https://fotoku-photos.s3.amazonaws.com/sessions/{$session->session_code}/frame/final_frame.jpg",
                        'email_sent_at' => $session->completed_at ? $session->completed_at->copy()->addMinutes(5) : null,
                        'metadata' => json_encode([
                            'dimensions' => 'A5 (148x210mm)',
                            'resolution' => '300dpi',
                            'color_profile' => 'sRGB',
                            'file_size' => rand(5, 10) . 'MB'
                        ]),
                        'created_at' => $session->completed_at,
                    ]);
                }

                // Create activity logs
                $activities = [
                    ['action' => 'session_created', 'description' => "Sesi foto {$session->session_code} dibuat untuk {$customer->name}"],
                ];

                if ($session->payment_status === 'paid') {
                    $paymentMethod = ['cash', 'qris', 'edc'][array_rand(['cash', 'qris', 'edc'])];
                    $activities[] = ['action' => 'payment_received', 'description' => "Pembayaran Rp " . number_format($session->total_price) . " diterima via {$paymentMethod}"];
                }

                if ($session->approved_at) {
                    $activities[] = ['action' => 'session_approved', 'description' => "Sesi {$session->session_code} disetujui untuk penggunaan photobox"];
                }

                if ($session->started_at) {
                    $activities[] = ['action' => 'session_started', 'description' => "Sesi foto {$session->session_code} dimulai di {$photobox->code}"];
                }

                if ($session->completed_at) {
                    $activities[] = ['action' => 'session_completed', 'description' => "Sesi foto {$session->session_code} selesai dan frame telah dibuat"];
                }

                foreach ($activities as $activity) {
                    ActivityLog::create([
                        'action' => $activity['action'],
                        'description' => $activity['description'],
                        'photo_session_id' => $session->id,
                        'user_id' => $admin->id,
                        'metadata' => json_encode(['session_code' => $session->session_code]),
                        'created_at' => $session->created_at,
                    ]);
                }
            }
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('- ' . count($sessions) . ' photo sessions');
        $this->command->info('- ' . Photo::count() . ' photos');
        $this->command->info('- ' . Frame::count() . ' frames');
        $this->command->info('- ' . PaymentLog::count() . ' payment logs');
        $this->command->info('- ' . ActivityLog::count() . ' activity logs');
    }

    private function weightedRandom($values, $weights)
    {
        $count = count($values);
        $i = 0;
        $n = 0;
        $num = mt_rand(0, array_sum($weights));
        while($i < $count){
            $n += $weights[$i];
            if($n >= $num){
                break;
            }
            $i++;
        }
        return $values[$i];
    }
}
