@extends('errors.layout')

@section('title', '503 - Layanan Tidak Tersedia')

@section('content')
<div class="space-y-6">
    <!-- Error Code -->
    <div class="text-center">
        <div class="text-6xl font-bold text-yellow-500 mb-2">503</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Layanan Tidak Tersedia</h2>
        <p class="text-gray-600">
            Fotoku sedang dalam masa pemeliharaan. Silakan coba lagi nanti.
        </p>
    </div>

    <!-- Friendly Message -->
    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">🔧</div>
            <div>
                <h3 class="font-semibold text-gray-800">Sedang Pemeliharaan</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Kami sedang melakukan pemeliharaan sistem untuk memberikan pengalaman yang lebih baik.
                </p>
            </div>
        </div>
    </div>

    <!-- What's happening -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-center">Apa yang sedang terjadi?</h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">⚙️</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Peningkatan Sistem</h4>
                    <p class="text-sm text-gray-600">Kami sedang meningkatkan performa dan fitur aplikasi</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="text-lg mt-1">🛡️</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Pembaruan Keamanan</h4>
                    <p class="text-sm text-gray-600">Penerapan patch keamanan terbaru untuk melindungi data Anda</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                <div class="text-lg mt-1">✨</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Fitur Baru</h4>
                    <p class="text-sm text-gray-600">Penambahan fitur baru untuk pengalaman yang lebih baik</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Estimated time -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 text-center">
        <h3 class="font-semibold text-gray-800 mb-2">Estimasi Waktu</h3>
        <div class="flex justify-center items-center space-x-2">
            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
            <p class="text-sm text-gray-600">
                Pemeliharaan biasanya selesai dalam 15-30 menit
            </p>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            Terakhir diperbarui: {{ now()->format('d/m/Y H:i') }} WIB
        </p>
    </div>

    <!-- What users can do -->
    <div class="space-y-3">
        <h3 class="font-semibold text-gray-800 text-center">Sementara itu, Anda bisa:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="bg-white p-3 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="text-2xl mb-2">☕</div>
                    <p class="font-medium">Istirahat sejenak</p>
                </div>
            </div>
            <div class="bg-white p-3 rounded-lg border border-gray-200">
                <div class="text-center">
                    <div class="text-2xl mb-2">🔔</div>
                    <p class="font-medium">Set reminder</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('actions')
<div class="space-y-3">
    <button onclick="setTimeout(() => location.reload(), 5000); this.innerHTML='Refresh dalam 5 detik...'; this.disabled=true;" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Auto Refresh
    </button>
    
    <button onclick="location.reload()" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Coba Sekarang
    </button>
    
    <div class="text-center">
        <p class="text-xs text-gray-500">
            Halaman akan otomatis refresh setiap 30 detik
        </p>
    </div>
</div>

<script>
    // Auto refresh every 30 seconds
    setTimeout(function() {
        location.reload();
    }, 30000);
</script>
@endsection
