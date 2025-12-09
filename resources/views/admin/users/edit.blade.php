@extends('admin.layout')

@section('header', 'Edit User')
@section('description', 'Update informasi user: ' . $user->name)

@section('content')
<style>
    :root{ --brand-teal:#053a63; --brand-orange:#f29223; --brand-curious:#1a90d6; --brand-dodger:#1fa8f0; }
    .brand-primary{ background-color:var(--brand-dodger); color:#fff; }
    .brand-primary:hover{ background-color:var(--brand-curious); }
    .input-brand:focus{ outline:none; border-color:var(--brand-dodger); box-shadow:0 0 0 3px rgba(31,168,240,.25); }
</style>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl p-8 shadow-sm border border-gray-100">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
               <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
               <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Telepon
                        </label>
               <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select name="role" required class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Customer</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <p class="mt-1 text-xs text-gray-500">Anda tidak dapat mengubah role sendiri</p>
                        @endif
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
                        <select name="status" required class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Diblokir</option>
                        </select>
                        @if($user->id === auth()->id())
                            <input type="hidden" name="status" value="{{ $user->status }}">
                            <p class="mt-1 text-xs text-gray-500">Anda tidak dapat mengubah status sendiri</p>
                        @endif
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Login -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Terakhir Login
                        </label>
                        <div class="px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600">
                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : 'Belum pernah login' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
                <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengubah password</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
               <input type="password" name="password"
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password
                        </label>
               <input type="password" name="password_confirmation"
                   class="w-full px-4 py-3 border border-gray-200 rounded-lg input-brand"
                               placeholder="Ulangi password baru">
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
                          placeholder="Catatan tambahan untuk user ini...">{{ old('notes', $user->notes) }}</textarea>
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
                    <i class="fas fa-save mr-2"></i>Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
