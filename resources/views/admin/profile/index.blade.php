@extends('admin.layout')

@section('title', 'Profil Admin')
@section('header', 'Profil Admin')
@section('description', 'Kelola profil dan keamanan akun admin.')

@section('content')
<style>
    /* Inputs: consistent brand focus */
    .input-palette {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        transition: box-shadow .2s, border-color .2s;
    }
    .input-palette:focus {
        outline: none;
        border-color: var(--brand-curious);
        box-shadow: 0 0 0 3px rgba(26,144,214,.2);
    }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; justify-content: center; font-weight: 600; border-radius: .5rem; transition: transform .15s ease, box-shadow .2s, background-color .2s; }
    .btn-primary { background: var(--brand-teal); color: #fff; }
    .btn-primary:hover { filter: brightness(1.05); box-shadow: 0 6px 18px rgba(5,58,99,.18); transform: translateY(-1px); }
    .btn-secondary { background: var(--brand-curious); color: #fff; }
    .btn-secondary:hover { filter: brightness(1.05); box-shadow: 0 6px 18px rgba(26,144,214,.18); transform: translateY(-1px); }
    .btn-accent { background: var(--brand-orange); color: #fff; }
    .btn-accent:hover { filter: brightness(1.05); box-shadow: 0 6px 18px rgba(242,146,35,.18); transform: translateY(-1px); }
    .btn-copied { background: #16a34a !important; color: #fff !important; }

    /* Avatars */
    .avatar-circle { background: rgba(26,144,214,.12); color: var(--brand-curious); }
    .avatar-lock { background: rgba(242,146,35,.15); color: var(--brand-orange); }

    /* Info box */
    .info-box { background: rgba(26,144,214,.07); border: 1px solid rgba(26,144,214,.25); border-radius: .5rem; }
    .info-title { color: var(--brand-curious); font-weight: 600; }
    .info-item { color: #0f3b4f; }

    /* Strength badges */
    .badge { padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: .75rem; font-weight: 600; }
    .badge-weak { background: #fee2e2; color: #dc2626; }
    .badge-medium { background: rgba(242,146,35,.15); color: var(--brand-orange); }
    .badge-strong { background: rgba(31,168,240,.15); color: var(--brand-dodger); }
    .badge-very-strong { background: rgba(5,58,99,.12); color: var(--brand-teal); }
</style>
<div class="space-y-6">
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center space-x-4">
          <div class="w-16 h-16 rounded-full flex items-center justify-center avatar-circle">
              <i class="fas fa-user text-2xl"></i>
          </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Informasi Profil</h1>
                    <p class="text-gray-600">Perbarui informasi profil dan alamat email</p>
                </div>
            </div>
        </div>

     <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
              <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}"
                  class="w-full input-palette @error('name') border-red-500 @enderror"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                  class="w-full input-palette @error('email') border-red-500 @enderror"
                           required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
              <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}"
                  class="w-full input-palette @error('phone') border-red-500 @enderror"
                           placeholder="08123456789">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
              <button type="submit" 
                class="w-full btn btn-primary py-3 px-6">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Password Security -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center space-x-4">
          <div class="w-16 h-16 rounded-full flex items-center justify-center avatar-lock">
              <i class="fas fa-lock text-2xl"></i>
          </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Keamanan Password</h2>
                    <p class="text-gray-600">Ubah password dengan standar keamanan tinggi</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Password Requirements Info -->
         <div class="info-box p-4 mb-6">
          <h3 class="text-sm info-title mb-2">
                    <i class="fas fa-info-circle mr-2"></i>
                    Persyaratan Password Keamanan Tinggi:
                </h3>
          <ul class="text-sm info-item space-y-1">
                    <li>• Minimal 12 karakter</li>
                    <li>• Kombinasi huruf besar dan kecil</li>
                    <li>• Mengandung angka</li>
                    <li>• Mengandung simbol (!@#$%^&*)</li>
                    <li>• Tidak terdapat dalam database password yang pernah bocor</li>
                </ul>
            </div>

            <!-- Manual Password Change -->
            <form method="POST" action="{{ route('admin.profile.password') }}" class="mb-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
               <input type="password" 
                               id="current_password" 
                               name="current_password" 
                   class="w-full input-palette @error('current_password') border-red-500 @enderror"
                               required>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <div class="relative">
                <input type="password" 
                                   id="password" 
                                   name="password" 
                    class="w-full input-palette @error('password') border-red-500 @enderror"
                                   required
                                   onkeyup="checkPasswordStrength(this.value)">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="password-toggle-icon"></i>
                            </button>
                        </div>
                        <div id="password-strength" class="mt-2 text-sm"></div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
               <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                   class="w-full input-palette"
                               required>
                    </div>

              <button type="submit" 
                class="btn btn-primary py-3 px-6">
                        <i class="fas fa-key mr-2"></i>
                        Ubah Password
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">ATAU</span>
                </div>
            </div>

            <!-- Password Generator -->
        <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-robot mr-2"></i>
                    Generator Password Otomatis
                </h3>
                <p class="text-sm text-gray-600 mb-4">
                    Buat password yang aman secara otomatis dengan standar keamanan tinggi.
                </p>
                
                <div class="space-y-4">
                    <div class="flex space-x-3">
            <button type="button" 
                                onclick="generateSecurePassword()" 
                class="btn btn-accent py-2 px-4">
                            <i class="fas fa-dice mr-2"></i>
                            Generate Password
                        </button>
            <button type="button" 
                                onclick="copyToClipboard('generated-password')" 
                class="btn btn-secondary py-2 px-4"
                                id="copy-button" 
                                style="display: none;">
                            <i class="fas fa-copy mr-2"></i>
                            Salin Password
                        </button>
                    </div>
                    
                    <div id="generated-password-container" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password yang Dihasilkan</label>
                        <div class="flex space-x-2">
                            <input type="text" 
                                   id="generated-password" 
                   class="flex-1 input-palette bg-white font-mono"
                                   readonly>
                            <button type="button" 
                                    onclick="useGeneratedPassword()" 
                    class="btn btn-primary py-3 px-4">
                                <i class="fas fa-arrow-up mr-2"></i>
                                Gunakan
                            </button>
                        </div>
                        <div id="generated-password-strength" class="mt-2 text-sm"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-toggle-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function checkPasswordStrength(password) {
    const strengthDiv = document.getElementById('password-strength');
    
    if (!password) {
        strengthDiv.innerHTML = '';
        return;
    }
    
    let score = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 12) {
        score += 2;
    } else if (password.length >= 8) {
        score += 1;
        feedback.push('Minimal 12 karakter disarankan');
    } else {
        feedback.push('Terlalu pendek (minimal 12 karakter)');
    }
    
    // Character variety checks
    if (/[a-z]/.test(password)) score += 1;
    else feedback.push('Perlu huruf kecil');
    
    if (/[A-Z]/.test(password)) score += 1;
    else feedback.push('Perlu huruf besar');
    
    if (/\d/.test(password)) score += 1;
    else feedback.push('Perlu angka');
    
    if (/[^a-zA-Z0-9]/.test(password)) score += 1;
    else feedback.push('Perlu simbol');
    
    // Complexity bonus
    if (/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/.test(password)) {
        score += 2;
    }
    
    let strength, badgeClass;
    if (score <= 3) {
        strength = 'Lemah';
        badgeClass = 'badge-weak';
    } else if (score <= 5) {
        strength = 'Sedang';
        badgeClass = 'badge-medium';
    } else if (score <= 7) {
        strength = 'Kuat';
        badgeClass = 'badge-strong';
    } else {
        strength = 'Sangat Kuat';
        badgeClass = 'badge-very-strong';
    }
    
    let html = `<div class="flex items-center space-x-2">
        <span class="badge ${badgeClass}">${strength}</span>`;
    
    if (feedback.length > 0) {
        html += `<span class="text-xs text-gray-500">${feedback.join(', ')}</span>`;
    }
    
    html += '</div>';
    strengthDiv.innerHTML = html;
}

function generateSecurePassword() {
    axios.post('/admin/profile/generate-password')
        .then(response => {
            const password = response.data.password;
            const strength = response.data.strength;
            
            document.getElementById('generated-password').value = password;
            document.getElementById('generated-password-container').style.display = 'block';
            document.getElementById('copy-button').style.display = 'inline-block';
            
            // Show strength
            const strengthDiv = document.getElementById('generated-password-strength');
            let badgeClass;
            if (strength === 'Sangat Kuat') {
                badgeClass = 'badge-very-strong';
            } else if (strength === 'Kuat') {
                badgeClass = 'badge-strong';
            } else {
                badgeClass = 'badge-medium';
            }
            
            strengthDiv.innerHTML = `
                <div class="flex items-center space-x-2">
                    <span class="badge ${badgeClass}">Kekuatan: ${strength}</span>
                    <span class="text-xs text-gray-500">Password siap digunakan</span>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error generating password:', error);
            alert('Gagal membuat password. Silakan coba lagi.');
        });
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999);
    
    try {
        document.execCommand('copy');
        
        // Visual feedback
        const button = document.getElementById('copy-button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Tersalin!';
        button.classList.add('btn-copied');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('btn-copied');
        }, 2000);
    } catch (err) {
        alert('Gagal menyalin password. Silakan salin manual.');
    }
}

function useGeneratedPassword() {
    const generatedPassword = document.getElementById('generated-password').value;
    
    if (!generatedPassword) {
        alert('Tidak ada password yang dihasilkan. Silakan generate terlebih dahulu.');
        return;
    }
    
    // Fill password fields
    document.getElementById('password').value = generatedPassword;
    document.getElementById('password_confirmation').value = generatedPassword;
    
    // Check strength
    checkPasswordStrength(generatedPassword);
    
    // Visual feedback
    alert('Password berhasil diisi! Jangan lupa masukkan password saat ini dan simpan perubahan.');
    
    // Focus on current password field
    document.getElementById('current_password').focus();
}
</script>
@endsection
