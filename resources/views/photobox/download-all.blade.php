<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Fotoku Photos - {{ $session->session_code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #164e63 50%, #059669 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <div class="min-h-screen p-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
                    <h1 class="text-4xl font-bold text-white mb-2">FOTOKU</h1>
                    <p class="text-white/80 text-lg">Your Photo Collection</p>
                    <div class="mt-4 text-white/70">
                        <p><strong>Session:</strong> {{ $session->session_code }}</p>
                        <p><strong>Customer:</strong> {{ $session->customer_name }}</p>
                        <p><strong>Date:</strong> {{ $session->created_at->format('d M Y, H:i') }}</p>
                        <p><strong>Total Photos:</strong> {{ $photos->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Download All Button -->
            <div class="text-center mb-8">
                @php
                $expires = time() + 86400; // 24 hours
                $signature = hash_hmac('sha256', $session->id . $expires, config('app.key'));
                @endphp
                <a href="{{ route('photobox.download-all-photos', [
                    'session' => $session->id, 
                    'expires' => $expires,
                    'signature' => $signature,
                    'zip' => 1
                ]) }}" 
                   class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg inline-block">
                    <i class="fas fa-download mr-2"></i>
                    Download All Photos as ZIP
                </a>
            </div>

            <!-- Photos Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($photos as $photo)
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-all duration-200">
                    <div class="aspect-square rounded-lg overflow-hidden mb-3 bg-gray-200">
                        <img src="{{ route('photobox.serve-photo', $photo->id) }}" 
                             alt="Photo {{ $photo->sequence_number }}" 
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-200"
                             loading="lazy">
                    </div>
                    <div class="text-center">
                        <p class="text-white/80 text-sm font-medium mb-2">Photo #{{ $photo->sequence_number }}</p>
                        <a href="{{ route('photobox.serve-photo', $photo->id) }}" 
                           download="fotoku-{{ $session->session_code }}-photo-{{ $photo->sequence_number }}.jpg"
                           class="inline-block bg-white/20 text-white px-3 py-1 rounded-lg text-xs hover:bg-white/30 transition-colors">
                            <i class="fas fa-download mr-1"></i>
                            Download
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Footer -->
            <div class="text-center mt-12 text-white/60">
                <p class="mb-2">© {{ date('Y') }} Fotoku - Capture Your Moments</p>
                <p class="text-sm">This page will expire on {{ now()->addDays(7)->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Photo data from server
        const sessionCode = '{{ $session->session_code }}';
        const photoData = [
            @foreach($photos as $photo)
            {
                id: {{ $photo->id }},
                sequence_number: {{ $photo->sequence_number }},
                url: '{{ route('photobox.serve-photo', $photo->id) }}'
            }@if(!$loop->last),@endif
            @endforeach
        ];
        
        async function downloadAllPhotos() {
            const button = event.target;
            const originalText = button.innerHTML;
            
            try {
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Preparing Download...';
                button.disabled = true;
                
                // For now, we'll trigger individual downloads
                // In a real implementation, you'd want to create a server-side ZIP
                for (let i = 0; i < photoData.length; i++) {
                    const photo = photoData[i];
                    const link = document.createElement('a');
                    link.href = photo.url;
                    link.download = `fotoku-${sessionCode}-photo-${photo.sequence_number}.jpg`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Small delay between downloads
                    if (i < photoData.length - 1) {
                        await new Promise(resolve => setTimeout(resolve, 500));
                    }
                }
                
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Download Complete!';
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 3000);
                
            } catch (error) {
                console.error('Download error:', error);
                button.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Download Failed';
                button.disabled = false;
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 3000);
            }
        }
    </script>
</body>
</html>
