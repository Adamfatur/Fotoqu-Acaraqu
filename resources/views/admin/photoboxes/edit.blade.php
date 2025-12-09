@extends('admin.layout')

@section('header', 'Edit Photobox')
@section('description', 'Edit informasi photobox ' . $photobox->code)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <!-- Back Button -->
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.photoboxes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.photoboxes.update', $photobox) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Header -->
            <div class="text-center">
                <div class="w-16 h-16 bg-green-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cube text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Edit Photobox</h2>
                <p class="text-gray-600">Perbarui informasi photobox {{ $photobox->code }}</p>
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    Kode Photobox <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="code" 
                       name="code" 
                       value="{{ old('code', $photobox->code) }}"
                       placeholder="Contoh: BOX-01"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('code') border-red-500 @enderror"
                       required>
                @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Photobox <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $photobox->name) }}"
                       placeholder="Contoh: Photobox Utama"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Location -->
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                    Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="location" 
                       name="location" 
                       value="{{ old('location', $photobox->location) }}"
                       placeholder="Contoh: Lobby Utama, Lt. 2"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                       required>
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select id="status" 
                        name="status" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('status') border-red-500 @enderror"
                        required>
                    <option value="active" {{ old('status', $photobox->status) === 'active' ? 'selected' : '' }}>🟢 Aktif</option>
                    <option value="inactive" {{ old('status', $photobox->status) === 'inactive' ? 'selected' : '' }}>� Tidak Aktif</option>
                    <option value="maintenance" {{ old('status', $photobox->status) === 'maintenance' ? 'selected' : '' }}>🟡 Maintenance</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Deskripsi tambahan tentang photobox ini..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description', $photobox->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-500 mr-3 mt-1"></i>
                    <div class="text-blue-800 text-sm">
                        <p class="font-semibold mb-2">Informasi Saat Ini:</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-blue-600">Dibuat</p>
                                <p class="font-medium">{{ $photobox->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-blue-600">Sesi Aktif</p>
                                <p class="font-medium">{{ $photobox->activePhotoSessions->count() }} sesi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex space-x-4 pt-6">
                <a href="{{ route('admin.photoboxes.index') }}" 
                   class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- Danger Zone -->
        @if($photobox->activePhotoSessions->count() === 0)
        <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Danger Zone</h3>
                <p class="text-red-700 text-sm mb-4">
                    Hapus photobox ini secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
                <form action="{{ route('admin.photoboxes.destroy', $photobox) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menghapus photobox {{ $photobox->code }}? Tindakan ini tidak dapat dibatalkan!')"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Photobox
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="mt-8 pt-8 border-t border-gray-200">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-800">Tidak Dapat Dihapus</h3>
                        <p class="text-yellow-700 text-sm">
                            Photobox tidak dapat dihapus karena memiliki {{ $photobox->activePhotoSessions->count() }} sesi aktif.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
