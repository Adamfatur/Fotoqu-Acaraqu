<?php

namespace App\Services;

use App\Models\PhotoSession;
use App\Models\SessionGif;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GifService
{
    /**
     * Generate a photobooth-style animated GIF from the first 5 photos of a session.
     * Runs synchronously; prefer dispatch via job for background generation.
     */
    public function generateSessionGif(PhotoSession $session): SessionGif
    {
        // Reuse existing placeholder if present, else create a new record
        $gif = $session->sessionGif()->first();
        if ($gif && $gif->status === 'completed') {
            return $gif;
        }
    if (!$gif) {
            $gif = SessionGif::create([
                'photo_session_id' => $session->id,
                'filename' => 'fotoku-'.$session->session_code.'-anim.gif',
        'status' => 'processing',
        'progress' => 0,
        'step' => 'queued',
            ]);
        } else {
        $gif->update(['status' => 'processing', 'error_message' => null, 'progress' => 0, 'step' => 'starting']);
        }

        try {
            // Collect 5 photos in sequence order
            $gif->update(['step' => 'collecting-photos', 'progress' => 5]);
            $photos = $session->photos()->orderBy('sequence_number')->take(5)->get();
            if ($photos->count() === 0) {
                throw new \RuntimeException('No photos for GIF');
            }

            // Prepare temporary frames directory
            $gif->update(['step' => 'preparing-temp', 'progress' => 10]);
            $tempDir = storage_path('app/temp/gif/'.$session->session_code);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Resize frames to a higher quality size for GIF (configurable, default 1080px height)
            $manager = new ImageManager(new Driver());
            $frameFiles = [];
            $count = max(1, $photos->count());
            foreach ($photos as $index => $photo) {
                // Prefer local, fallback to S3
                if (!empty($photo->local_path) && file_exists($photo->local_path)) {
                    $content = file_get_contents($photo->local_path);
                } else {
                    $content = Storage::disk('s3')->get($photo->s3_path);
                }
                $img = $manager->read($content);
                $targetHeight = (int) config('fotoku.gif.height', 1080);
                $img = $img->scaleDown(height: $targetHeight); // keep aspect ratio

        // Apply watermark image if present
        try {
                    $wmPath = public_path('watermark-fotoku.png');
                    if (file_exists($wmPath)) {
                        $wm = $manager->read($wmPath);
                        // Scale watermark to ~20% of frame width and place bottom-right with margin
                        $targetWidth = max(1, (int) round($img->width() * 0.2));
                        $wm = $wm->scaleDown(width: $targetWidth);
                        $margin = 16;
                        $posX = $img->width() - $wm->width() - $margin;
                        $posY = $img->height() - $wm->height() - $margin;
            $img->place($wm, 'top-left', $posX, $posY);

                        // NOTE: second/prospek watermark removed per request - only Fotoku watermark is applied here
                    }
                } catch (\Throwable $e) {
                    \Log::warning('GifService: Watermark overlay skipped', ['error' => $e->getMessage()]);
                }
                // Use PNG (lossless) for intermediate frames to preserve fidelity
                $framePath = $tempDir.'/frame-'.sprintf('%02d', $index+1).'.png';
                $img->toPng()->save($framePath);
                $frameFiles[] = $framePath;
                // Update progress in 10..50 range during frame prep
                $frameProgress = 10 + (int) floor(40 * (($index + 1) / $count));
                $gif->update(['progress' => min(50, $frameProgress), 'step' => 'preparing-frames']);
            }

            // Build animated GIF using Imagick if available
            $gifBinary = null;
            if (class_exists(\Imagick::class)) {
                $gif->update(['step' => 'assembling-gif', 'progress' => 60]);
                $imagick = new \Imagick();
                $imagick->setFormat('gif');
                // Higher quality palette handling
                $imagick->setOption('gif:optimize-transparency', 'false');

                // Timing from config (centiseconds per frame)
                $fps = max(1, (int) config('fotoku.gif.fps', 10));
                $delayCs = max(1, (int) round(100 / $fps));

                // Use only the captured frames to preserve the original pacing
                $lastIndex = count($frameFiles) - 1;
                foreach ($frameFiles as $idx => $file) {
                    $frame = new \Imagick($file);
                    // Slightly longer delay on last frame to soften the loop return
                    $frame->setImageDelay($idx === $lastIndex ? (int) round($delayCs * 1.6) : $delayCs);
                    $frame->setImageDispose(2); // standard background restore
                    $imagick->addImage($frame);
                    // tick progress a bit
                    if ($idx % max(1, (int) floor(max(1, count($frameFiles)) / 4)) === 0) {
                        $gif->update(['progress' => min(80, ($gif->progress ?? 60) + 2)]);
                    }
                }
                $imagick = $imagick->coalesceImages();
                // Quantize to 256 colors with dithering for smoother gradients
                foreach ($imagick as $frame) {
                    $frame->quantizeImage(256, \Imagick::COLORSPACE_RGB, 0, true, false);
                }
                // Try to optimize layers without aggressive lossy changes
                $imagick = $imagick->optimizeImageLayers();
                $gifBinary = $imagick->getImagesBlob();
                $imagick->clear();
                $imagick->destroy();
            } else {
                throw new \RuntimeException('Imagick extension not available for GIF creation');
            }

            // Save locally and upload to S3
            $gif->update(['step' => 'saving', 'progress' => 85]);
            $localDir = storage_path('app/private/photobox/'.$session->session_code);
            if (!is_dir($localDir)) {
                mkdir($localDir, 0755, true);
            }
            $localPath = $localDir.'/'.$gif->filename;
            file_put_contents($localPath, $gifBinary);
            $gif->update(['progress' => 90]);
            $s3Path = 'gifs/'.$session->session_code.'/'.$gif->filename;
            Storage::disk('s3')->put($s3Path, $gifBinary, 'public');

            $gif->update([
                'local_path' => $localPath,
                's3_path' => $s3Path,
                'file_size' => filesize($localPath) ?: null,
                'status' => 'completed',
                'progress' => 100,
                'step' => 'completed-wm1',
            ]);

            return $gif;
        } catch (\Throwable $e) {
            $gif->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'step' => 'failed',
            ]);
            \Log::error('GIF generation failed', [
                'session_id' => $session->id,
                'code' => $session->session_code,
                'error' => $e->getMessage(),
            ]);
            return $gif;
        }
    }
}
