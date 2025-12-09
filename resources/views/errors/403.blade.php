@extends('errors.layout')

@section('title', '403 - Akses Ditolak')

@section('content')
<div class="space-y-6">
    <!-- Error Code -->
    <div class="text-center">
        <div class="text-6xl font-bold text-orange-500 mb-2">403</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Akses Ditolak</h2>
        <p class="text-gray-600">
            Anda tidak memiliki izin untuk mengakses halaman ini.
        </p>
    </div>

    <!-- Friendly Message -->
    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">🔒</div>
            <div>
                <h3 class="font-semibold text-gray-800">Area Terbatas</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Halaman ini memerlukan hak akses khusus yang tidak Anda miliki.
                </p>
            </div>
        </div>
    </div>

    <!-- What to do -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-center">Yang dapat Anda lakukan:</h3>
        <div class="space-y-3">
            @guest
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">🔑</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Login ke akun Anda</h4>
                    <p class="text-sm text-gray-600">Masuk dengan akun yang memiliki hak akses</p>
                </div>
            </div>
            @else
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">👤</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hubungi Administrator</h4>
                    <p class="text-sm text-gray-600">Minta hak akses tambahan jika diperlukan</p>
                </div>
            </div>
            @endguest
            
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="text-lg mt-1">🏠</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Kembali ke area yang diizinkan</h4>
                    <p class="text-sm text-gray-600">Akses halaman yang Anda miliki izinnya</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-lg mt-1">📋</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Periksa URL yang benar</h4>
                    <p class="text-sm text-gray-600">Pastikan alamat halaman yang diakses sudah tepat</p>
                </div>
            </div>
        </div>
    </div>

    @auth
    <!-- User Info -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Saat ini Anda login sebagai: <strong>{{ auth()->user()->name }}</strong>
            </p>
            <p class="text-xs text-gray-500 mt-1">
                Role: {{ auth()->user()->role ?? 'User' }}
            </p>
        </div>
    </div>
    @endauth
</div>
@endsection

@section('actions')
<div class="space-y-3">
    @guest
    <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
        </svg>
        Login ke Akun
    </a>
    @else
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Kembali ke Dashboard
    </a>
    @endguest
    
    <div class="text-center">
        <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-800 transition-colors duration-200 text-sm font-medium">
            ← Kembali ke halaman sebelumnya
        </button>
    </div>
</div>
@endsection
