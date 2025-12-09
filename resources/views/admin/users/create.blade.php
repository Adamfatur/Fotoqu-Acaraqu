@extends('admin.layout')

@section('header', 'Tambah User Baru')
@section('description', 'Buat akun admin, manager, operator, atau customer baru')

@section('content')
<style>
    :root{ --brand-teal:#053a63; --brand-orange:#f29223; --brand-curious:#1a90d6; --brand-dodger:#1fa8f0; }
    .brand-primary{ background-color:var(--brand-dodger); color:#fff; }
    .brand-primary:hover{ background-color:var(--brand-curious); }
    .input-brand:focus{ outline:none; border-color:var(--brand-dodger); box-shadow:0 0 0 3px rgba(31,168,240,.25); }
</style>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
               <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
               <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="user@fotoku.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Telepon
                        </label>
               <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="08xxxxxxxxxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand">
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Role Permissions Info -->
            <div class="rounded-lg p-4" style="background:#e9f5fd; border:1px solid #bfdef3;">
                <h4 class="font-medium mb-2" style="color:#1a90d6">Hak Akses Role:</h4>
                <div class="text-sm space-y-1" style="color:#2563eb">
                    <div><strong>Administrator:</strong> Akses penuh semua fitur termasuk manajemen user</div>
                    <div><strong>Manager:</strong> Manajemen sesi, customer, laporan, dan pengaturan photobox</div>
                    <div><strong>Operator:</strong> Operasional photobox dan monitoring sesi</div>
                    <div><strong>Customer:</strong> Hanya akses interface photobox</div>
                </div>
            </div>

            <!-- Account Settings -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Akun</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" required class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Password</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
               <input type="password" name="password" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password <span class="text-red-500">*</span>
                        </label>
               <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan
                </label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                          placeholder="Catatan tambahan untuk user ini...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                
        <button type="submit" 
            class="px-6 py-3 rounded-lg transition-colors brand-primary">
                    <i class="fas fa-save mr-2"></i>Simpan User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
