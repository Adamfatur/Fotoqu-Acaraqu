@extends('errors.layout')

@section('title', '419 - Sesi Telah Berakhir')

@section('content')
<div class="space-y-6">
    <!-- Error Code -->
    <div class="text-center">
        <div class="text-6xl font-bold text-purple-500 mb-2">419</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Sesi Telah Berakhir</h2>
        <p class="text-gray-600">
            Sesi Anda telah habis. Silakan refresh halaman dan coba lagi.
        </p>
    </div>

    <!-- Friendly Message -->
    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">⏰</div>
            <div>
                <h3 class="font-semibold text-gray-800">Keamanan Sesi</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Untuk keamanan, sesi Anda telah habis setelah periode tidak aktif.
                </p>
            </div>
        </div>
    </div>

    <!-- What happened -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-center">Apa yang terjadi?</h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">🔐</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Token Keamanan Habis</h4>
                    <p class="text-sm text-gray-600">Sistem menggunakan token keamanan yang memiliki batas waktu</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-lg mt-1">⚡</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Halaman Terlalu Lama Terbuka</h4>
                    <p class="text-sm text-gray-600">Halaman ini sudah terbuka terlalu lama tanpa aktivitas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Solution -->
    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
        <div class="text-center">
            <h3 class="font-semibold text-gray-800 mb-2">Solusi Mudah</h3>
            <p class="text-sm text-gray-600">
                Klik tombol "Refresh Halaman" di bawah ini untuk mendapatkan token keamanan yang baru
            </p>
        </div>
    </div>
</div>
@endsection

@section('actions')
<div class="space-y-3">
    <button onclick="location.reload()" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Refresh Halaman
    </button>
    
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Kembali ke Beranda
    </a>
    
    <div class="text-center">
        <p class="text-xs text-gray-500">
            Atau tutup browser dan buka kembali aplikasi Fotoku
        </p>
    </div>
</div>
@endsection
