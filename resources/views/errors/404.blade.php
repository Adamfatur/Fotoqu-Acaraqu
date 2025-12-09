@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="space-y-6">
    <!-- Error Code -->
    <div class="text-center">
        <div class="text-6xl font-bold text-fotoku-primary mb-2">404</div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-600">
            Maaf, halaman yang Anda cari tidak dapat ditemukan.
        </p>
    </div>

    <!-- Friendly Message -->
    <div class="bg-pastel-yellow/50 rounded-lg p-4 border border-yellow-200">
        <div class="flex items-center space-x-3">
            <div class="text-2xl">📸</div>
            <div>
                <h3 class="font-semibold text-gray-800">Ups! Sepertinya foto ini hilang...</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Tapi jangan khawatir! Kami dapat membantu Anda menemukan halaman yang tepat.
                </p>
            </div>
        </div>
    </div>

    <!-- Helpful Links -->
    <div class="space-y-3">
        <h3 class="font-semibold text-gray-800 text-center">Mungkin Anda mencari:</h3>
        <div class="grid grid-cols-1 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center space-x-2 p-3 bg-fotoku-primary/10 hover:bg-fotoku-primary/20 rounded-lg transition-colors duration-200 border border-fotoku-primary/20">
                <span class="text-lg">🏠</span>
                <span class="font-medium text-fotoku-primary">Dashboard Admin</span>
            </a>
            
            @if(Route::has('admin.photoboxes.index'))
            <a href="{{ route('admin.photoboxes.index') }}" class="flex items-center justify-center space-x-2 p-3 bg-fotoku-secondary/10 hover:bg-fotoku-secondary/20 rounded-lg transition-colors duration-200 border border-fotoku-secondary/20">
                <span class="text-lg">📦</span>
                <span class="font-medium text-fotoku-secondary">Kelola Photobox</span>
            </a>
            @endif
            
            @if(Route::has('admin.sessions.index'))
            <a href="{{ route('admin.sessions.index') }}" class="flex items-center justify-center space-x-2 p-3 bg-fotoku-accent/10 hover:bg-fotoku-accent/20 rounded-lg transition-colors duration-200 border border-fotoku-accent/20">
                <span class="text-lg">📋</span>
                <span class="font-medium text-fotoku-accent">Sesi Foto</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection

@section('actions')
<div class="space-y-3">
    <a href="{{ url('/') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-fotoku-primary hover:bg-fotoku-primary/90 text-white font-semibold rounded-lg transition-colors duration-200 shadow-lg">
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
