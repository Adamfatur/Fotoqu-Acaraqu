@extends('admin.layout')

@section('header', 'Tambah Photobox Baru')
@section('description', 'Tambahkan photobox baru ke dalam sistem')

@section('content')
<style>
    /* Menggunakan style yang konsisten dengan halaman-halaman sebelumnya */
    :root {
        --page-bg: #f7f9fc;
        --border-color: #e9eef5;
        --navy-color: #1e3a8a;
        --text-primary: #2c3e50;
        --text-secondary: #7f8c8d;
        --input-bg: #fdfdfe;
    }
    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        background: var(--input-bg);
        border: 1px solid #dce4f2;
        border-radius: 0.75rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--navy-color);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
    }
    .info-box {
        background-color: #f0f5ff;
        border-left: 4px solid #60a5fa; /* Blue accent */
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
    }
</style>

<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Photobox Baru</h1>
                <p class="text-gray-500 mt-1">Isi detail di bawah untuk mendaftarkan photobox baru.</p>
            </div>
            <a href="{{ route('admin.photoboxes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold border border-gray-200 hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <form action="{{ route('admin.photoboxes.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Unik <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}"
                           placeholder="Contoh: PIM-01"
                           class="form-input @error('code') border-red-500 @enderror" required>
                    @error('code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Photobox <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="Contoh: Photobox PIM 1"
                           class="form-input @error('name') border-red-500 @enderror" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}"
                           placeholder="Contoh: Pondok Indah Mall 1, Lt. 2"
                           class="form-input @error('location') border-red-500 @enderror" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Deskripsi tambahan tentang photobox, misal: 'Dekat eskalator utara'"
                              class="form-input @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="info-box">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-4 mt-1"></i>
                    <div class="text-blue-800 text-sm">
                        Status awal untuk photobox baru akan secara otomatis diatur ke **"Tersedia"**. Anda dapat mengubahnya nanti di halaman manajemen.
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.photoboxes.index') }}" 
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-8 py-2.5 bg-navy-600 text-white rounded-lg hover:bg-navy-700 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-105" style="background-color: var(--navy-color);">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Photobox
                </button>
            </div>
        </form>
    </div>
</div>
@endsection