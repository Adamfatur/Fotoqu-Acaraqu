@extends('errors.layout')

@section('title', '500 - Kesalahan Server')

@section('content')
<div class="space-y-6">
    <!-- Error Code -->
    <div class="text-center">
        <div class="text-6xl font-bold text-red-500 mb-2">500</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Kesalahan Server</h2>
        <p class="text-gray-600">
            Terjadi kesalahan pada server. Kami sedang memperbaikinya.
        </p>
    </div>

    <!-- Friendly Message -->
    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">🔧</div>
            <div>
                <h3 class="font-semibold text-gray-800">Sedang dalam perbaikan...</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Tim teknis kami telah diberitahu dan sedang menangani masalah ini.
                </p>
            </div>
        </div>
    </div>

    <!-- What to do -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-center">Yang dapat Anda lakukan:</h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">🔄</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Coba lagi setelah beberapa saat</h4>
                    <p class="text-sm text-gray-600">Masalah ini biasanya bersifat sementara</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="text-lg mt-1">🏠</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Kembali ke halaman utama</h4>
                    <p class="text-sm text-gray-600">Mulai dari dashboard untuk akses yang aman</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-lg mt-1">📞</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hubungi support jika masalah berlanjut</h4>
                    <p class="text-sm text-gray-600">Tim kami siap membantu 24/7</p>
                </div>
            </div>
        </div>
    </div>

    @if(config('app.debug') && isset($exception))
    <!-- Debug Info (only in development) -->
    <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-300">
        <details class="text-sm">
            <summary class="font-semibold text-gray-700 cursor-pointer hover:text-gray-900">
                Debug Information (Development Mode)
            </summary>
            <div class="mt-2 text-xs text-gray-600 font-mono bg-white p-3 rounded border">
                <strong>Error:</strong> {{ $exception->getMessage() }}<br>
                <strong>File:</strong> {{ $exception->getFile() }}<br>
                <strong>Line:</strong> {{ $exception->getLine() }}
            </div>
        </details>
    </div>
    @endif
</div>
@endsection

@section('actions')
<div class="space-y-3">
    <button onclick="location.reload()" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Coba Lagi
    </button>
    
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Kembali ke Beranda
    </a>
</div>
@endsection
