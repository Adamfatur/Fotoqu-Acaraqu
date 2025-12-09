@extends('admin.layout')

@section('header', 'Pengaturan Sistem')
@section('description', 'Konfigurasi dan pengaturan aplikasi Fotoku')

@section('content')
<style>
    /* * REDESIGN: Vertical Tab Concept
     * A complete conceptual overhaul for a cleaner, more organized settings page.
     */

    :root {
        /* Base (palette centralized in admin layout) */
        --color-white: #ffffff;
        --color-bg: #f5f9fc; /* light tinted bg to avoid pure white */
        --color-border: #e5e7eb; /* gray-200 */
        --color-text-primary: #1f2937; /* gray-800 */
        --color-text-secondary: #6b7280; /* gray-500 */
    }

    .settings-page-container {
        padding: 1.5rem;
        background-color: var(--color-bg);
        min-height: calc(100vh - 120px);
    }
    
    .settings-layout {
        display: flex;
        gap: 2rem;
        background-color: var(--color-white);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        overflow: hidden;
        min-height: 70vh;
        border: 1px solid var(--color-border);
    }

    /* Vertical Navigation Tabs */
    .settings-nav {
        background-color: var(--brand-teal);
        padding: 1.5rem 0.5rem;
        width: 260px;
        flex-shrink: 0;
        border-right: 1px solid rgba(255,255,255,0.08);
    }

    .settings-nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin: 0.25rem 0;
        border-radius: 0.5rem;
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .settings-nav-link i {
        width: 24px;
        margin-right: 1rem;
        text-align: center;
    }
    .settings-nav-link:hover {
        background-color: rgba(255,255,255,0.12);
        color: #ffffff;
    }
    .settings-nav-link.active {
        background: linear-gradient(90deg, var(--brand-curious), var(--brand-dodger));
        color: #ffffff;
        font-weight: 600;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,0.2);
    }

    /* Content Panels */
    .settings-content-wrapper {
        flex-grow: 1;
        padding: 2.5rem 2rem;
        overflow-y: auto;
    }
    
    .settings-panel {
        display: none; /* Hidden by default */
        animation: fadeIn 0.4s ease;
    }

    .settings-panel.active {
        display: block; /* Shown when active */
    }
    
    .panel-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-text-primary);
    }
    .panel-header p {
        color: var(--color-text-secondary);
        margin-top: 0.25rem;
        margin-bottom: 2.5rem;
    }

    .setting-item {
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid var(--color-border);
    }
    .setting-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    /* Re-styled Form Elements */
    .form-input {
        width: 100%;
        max-width: 500px;
        background: #f9fafb;
        border: 1px solid var(--color-border);
        border-radius: 0.5rem;
        padding: 10px 16px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-input:focus { outline: none; border-color: var(--brand-dodger); box-shadow: 0 0 0 3px rgba(31, 168, 240, 0.25); }

    .toggle-switch { /* Identical to previous good design */
        position: relative; display: inline-block; width: 50px; height: 28px;
    }
    .toggle-switch input { display: none; }
    .slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #e5e7eb; border-radius: 28px; transition: background-color .3s;
    }
    .slider:before {
        position: absolute; content: ""; height: 20px; width: 20px; left: 4px; bottom: 4px;
        background-color: white; border-radius: 50%; transition: transform .3s;
    }
    input:checked + .slider { background-color: var(--brand-dodger); }
    input:checked + .slider:before { transform: translateX(22px); }

    /* Action Footer */
    .settings-footer {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--color-border);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.75rem;
    }

    .btn {
        border: none; padding: 10px 20px; border-radius: 0.5rem; font-weight: 600;
        transition: all 0.2s ease; cursor: pointer; display: inline-flex;
        align-items: center; justify-content: center;
    }
    .btn-primary { background-color: var(--brand-dodger); color: #ffffff; }
    .btn-primary:hover { background-color: var(--brand-curious); color: #ffffff; }

    .btn-danger { background-color: var(--brand-orange); color: #1f2937; }
    .btn-danger:hover { background-color: #e7881c; color: #111827; }
    
    .btn-secondary {
        background-color: #e2e8f0; color: #475569;
    }
    .btn-secondary:hover { background-color: #cbd5e0; }

    /* For animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modal and Notification styles can be reused from the previous version as they are good */
    .notification { position: fixed; top: 20px; right: 20px; padding: 16px 24px; border-radius: 0.75rem; color: white; font-weight: 600; z-index: 1000; transform: translateY(-100px); opacity: 0; transition: transform 0.4s ease, opacity 0.4s ease; }
    .notification.show { transform: translateY(0); opacity: 1; }
    .notification.success { background-color: var(--brand-curious); }
    .notification.error { background-color: #ef4444; }
    .modal-overlay { position: fixed; inset: 0; background-color: rgba(26, 144, 214, 0.25); display: flex; align-items: center; justify-content: center; z-index: 50; opacity: 0; transition: opacity 0.3s ease; visibility: hidden; }
    .modal-overlay.show { opacity: 1; visibility: visible; }
    .modal-box { background: #fff; border-radius: 1rem; padding: 2rem; max-width: 420px; width: 90%; text-align: center; transform: scale(0.95); transition: transform 0.3s ease; }
    .modal-overlay.show .modal-box { transform: scale(1); }
</style>

<div class="settings-page-container">
    @if (session('success'))
        <div class="notification success show" id="successNotification"><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="notification error show" id="errorNotification"><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')
        <div class="settings-layout">
            <nav class="settings-nav">
                @foreach($settingGroups as $group => $settings)
                    <a href="#{{ $group }}" class="settings-nav-link {{ $loop->first ? 'active' : '' }}" data-target-panel="{{ $group }}">
                        @if($group === 'general')
                            <i class="fas fa-cog"></i> <span>Aplikasi</span>
                        @elseif($group === 'photo')
                            <i class="fas fa-camera"></i> <span>Foto & Kamera</span>
                        @elseif($group === 'email')
                            <i class="fas fa-envelope"></i> <span>Email & Notifikasi</span>
                        @elseif($group === 'payment')
                            <i class="fas fa-credit-card"></i> <span>Pembayaran</span>
                        @elseif($group === 'system')
                            <i class="fas fa-server"></i> <span>Sistem</span>
                        @else
                            <i class="fas fa-sliders-h"></i> <span>{{ ucfirst($group) }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="settings-content-wrapper">
                @foreach($settingGroups as $group => $settings)
                <div id="{{ $group }}" class="settings-panel {{ $loop->first ? 'active' : '' }}">
                    <div class="panel-header">
                        @if($group === 'general')
                            <h3>Pengaturan Aplikasi</h3><p>Pengaturan umum terkait nama, logo, dan fungsionalitas aplikasi.</p>
                        @elseif($group === 'photo')
                            <h3>Pengaturan Foto & Kamera</h3><p>Konfigurasi kualitas foto, watermark, dan opsi kamera default.</p>
                        @elseif($group === 'email')
                            <h3>Pengaturan Email & Notifikasi</h3><p>Konfigurasi server SMTP dan template notifikasi email.</p>
                        @elseif($group === 'payment')
                            <h3>Pengaturan Pembayaran</h3><p>Atur gateway pembayaran dan mata uang yang digunakan.</p>
                        @else
                            <h3>Pengaturan Sistem</h3><p>Pengaturan tingkat lanjut untuk pemeliharaan dan keamanan sistem.</p>
                        @endif
                    </div>

                    @foreach($settings as $setting)
                    <div class="setting-item">
                        <label for="{{ $setting->key }}" class="block text-sm font-semibold text-gray-800 mb-1">
                            {{ $setting->label }}
                        </label>
                        @if($setting->description)
                        <p class="text-xs text-gray-500 mb-3">{{ $setting->description }}</p>
                        @endif

                        @if($setting->type === 'boolean')
                            <div class="flex items-center space-x-3">
                                <label class="toggle-switch">
                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="0">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" {{ $setting->value ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                                <span class="text-sm font-medium text-gray-600 toggle-label">{{ $setting->value ? 'Aktif' : 'Nonaktif' }}</span>
                            </div>
                        @elseif($setting->type === 'integer')
                            <input type="number" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" min="0" class="form-input">
                        @elseif($setting->type === 'text')
                            <input type="text" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                        @elseif($setting->type === 'email')
                            <input type="email" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                        @elseif($setting->type === 'url')
                            <input type="url" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                        @elseif($setting->type === 'textarea')
                            <textarea id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" rows="3" class="form-input">{{ $setting->value }}</textarea>
                        @elseif($setting->type === 'select')
                            <select id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" class="form-input">
                                @if($setting->options)
                                    @foreach(json_decode($setting->options, true) as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" {{ $setting->value == $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @else
                            <input type="text" id="{{ $setting->key }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                        @endif
                        
                        @error("settings.{$setting->key}")
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

        <div class="settings-footer">
             <button type="button" onclick="showResetModal()" class="btn btn-danger mr-auto"><i class="fas fa-undo mr-2"></i> Reset</button>
             <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i> Simpan Pengaturan</button>
        </div>
    </form>
</div>

<div id="resetModal" class="modal-overlay">
    <div class="modal-box">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Reset Semua Pengaturan?</h3>
        <p class="text-sm text-gray-500 mb-6">Tindakan ini akan mengembalikan semua pengaturan ke nilai default. Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex space-x-4">
            <button type="button" onclick="closeResetModal()" class="flex-1 btn btn-secondary">Batal</button>
            <form action="{{ route('admin.settings.reset') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full btn btn-danger">Ya, Reset</button>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- NEW: Vertical Tab Switching Logic ---
    const navLinks = document.querySelectorAll('.settings-nav-link');
    const panels = document.querySelectorAll('.settings-panel');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetPanelId = this.getAttribute('data-target-panel');

            // Update active state for links
            navLinks.forEach(navLink => navLink.classList.remove('active'));
            this.classList.add('active');

            // Update active state for panels
            panels.forEach(panel => {
                if (panel.id === targetPanelId) {
                    panel.classList.add('active');
                } else {
                    panel.classList.remove('active');
                }
            });
        });
    });

    // --- Existing good logic (unchanged) ---
    const notifications = document.querySelectorAll('.notification.show');
    notifications.forEach(notification => {
        setTimeout(() => notification.classList.remove('show'), 5000);
    });

    const form = document.getElementById('settingsForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            submitButton.disabled = true;
        });
    }

    const toggles = document.querySelectorAll('.toggle-switch input');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const label = this.closest('.flex').querySelector('.toggle-label');
            if (label) {
                label.textContent = this.checked ? 'Aktif' : 'Nonaktif';
            }
        });
    });
});

const resetModal = document.getElementById('resetModal');
function showResetModal() {
    if (resetModal) resetModal.classList.add('show');
}
function closeResetModal() {
    if (resetModal) resetModal.classList.remove('show');
}
window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && resetModal && resetModal.classList.contains('show')) {
        closeResetModal();
    }
});
</script>
@endsection