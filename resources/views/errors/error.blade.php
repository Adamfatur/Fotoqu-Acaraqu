@extends('errors.layout')

@section('title', 'Terjadi Kesalahan')

@section('content')
<div class="space-y-6">
    <!-- Error Icon -->
    <div class="text-center">
        <div class="text-6xl mb-4">😔</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Ups! Terjadi Kesalahan</h2>
        <p class="text-gray-600">
            Mohon maaf, terjadi kesalahan yang tidak terduga. Tim kami akan segera memperbaikinya.
        </p>
    </div>

    <!-- Error Details -->
    @if(isset($exception) && $exception)
    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">⚠️</div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-800">Detail Kesalahan</h3>
                <p class="text-sm text-gray-600 mt-1">
                    @if(config('app.debug'))
                        {{ $exception->getMessage() ?: 'Kesalahan tidak diketahui' }}
                    @else
                        Terjadi kesalahan sistem. Tim teknis telah diberitahu.
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- What to do -->
    <div class="space-y-4">
        <h3 class="font-semibold text-gray-800 text-center">Yang dapat Anda lakukan:</h3>
        <div class="space-y-3">
            <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-lg mt-1">🔄</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Refresh halaman</h4>
                    <p class="text-sm text-gray-600">Mungkin ini hanya masalah sementara</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-200">
                <div class="text-lg mt-1">🏠</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Kembali ke beranda</h4>
                    <p class="text-sm text-gray-600">Mulai dari halaman utama</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="text-lg mt-1">⏰</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Coba lagi nanti</h4>
                    <p class="text-sm text-gray-600">Beri waktu untuk sistem memulih</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                <div class="text-lg mt-1">📞</div>
                <div>
                    <h4 class="font-semibold text-gray-800">Hubungi support</h4>
                    <p class="text-sm text-gray-600">Jika masalah terus berlanjut</p>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
        <div class="text-center">
            <h3 class="font-semibold text-gray-800 mb-2">Informasi Sistem</h3>
            <div class="space-y-1 text-sm text-gray-600">
                <p>Waktu: {{ now()->format('d/m/Y H:i:s') }} WIB</p>
                <p>ID Sesi: {{ session()->getId() ? Str::limit(session()->getId(), 8) : 'N/A' }}</p>
                @auth
                <p>User: {{ auth()->user()->name }} ({{ auth()->user()->role ?? 'User' }})</p>
                @endauth
            </div>
        </div>
    </div>

    @if(config('app.debug') && isset($exception))
    <!-- Debug Info (Development Only) -->
    <div class="bg-gray-100 rounded-lg p-4 border border-gray-300">
        <details class="text-sm">
            <summary class="font-semibold text-gray-700 cursor-pointer hover:text-gray-900 mb-2">
                🔧 Debug Information (Development Mode)
            </summary>
            <div class="bg-white p-3 rounded border font-mono text-xs space-y-2">
                <div>
                    <strong>Exception:</strong> {{ get_class($exception) }}
                </div>
                <div>
                    <strong>Message:</strong> {{ $exception->getMessage() }}
                </div>
                <div>
                    <strong>File:</strong> {{ $exception->getFile() }}
                </div>
                <div>
                    <strong>Line:</strong> {{ $exception->getLine() }}
                </div>
                <div>
                    <strong>URL:</strong> {{ request()->fullUrl() }}
                </div>
                <div>
                    <strong>Method:</strong> {{ request()->method() }}
                </div>
                @if($exception->getCode())
                <div>
                    <strong>Code:</strong> {{ $exception->getCode() }}
                </div>
                @endif
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
        Refresh Halaman
    </button>
    
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Kembali ke Beranda
    </a>
    
    <div class="text-center">
        <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-800 transition-colors duration-200 text-sm font-medium">
            ← Kembali ke halaman sebelumnya
        </button>
    </div>
</div>
@endsection
