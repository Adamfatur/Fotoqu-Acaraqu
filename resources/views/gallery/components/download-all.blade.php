{{-- Download All Component --}}
<div class="text-center border-t border-gray-200 pt-8">
    <div class="mb-6">
        <h3 class="text-2xl font-bold mb-2" style="background: linear-gradient(135deg, var(--fotoku-blue), var(--fotoku-blue-dark)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Unduh Koleksi Lengkap
        </h3>
        <p class="text-gray-700">Dapatkan semua foto dalam satu paket premium</p>
    </div>
    
    @php
    // Create secure download link with expiry (24 hours)
    $expires = time() + (24 * 60 * 60);
    $signature = hash_hmac('sha256', $photoSession->id . $expires, config('app.key'));
    
    $downloadUrl = route('gallery.download.all', [
        'session' => $photoSession->session_code, 
        'expires' => $expires,
        'signature' => $signature
    ]);
    @endphp
    
    <a href="{{ $downloadUrl }}" 
       class="ios-btn ios-btn-primary text-lg px-12 py-4 mb-6">
        <i class="fas fa-archive"></i>
        Unduh Semua Foto (ZIP)
    </a>
    
    <div class="ios-card p-4 bg-gradient-to-r from-green-500/10 to-blue-500/10 max-w-xl mx-auto">
        <p class="text-gray-700 flex items-center justify-center text-sm">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <span class="font-medium">File ZIP berisi semua foto dalam resolusi penuh</span>
        </p>
    </div>
</div>
