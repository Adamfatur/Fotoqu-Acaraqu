<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'FotoQu') }} - Admin Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Toast Container (positioned below header via script) -->
    <div id="toast-root" class="fixed right-4 z-[9999] space-y-2 pointer-events-none" style="top: 84px;"></div>
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Favicon & Theme Color -->
    <link rel="icon" type="image/png" href="{{ asset('logo-fotoku-favicon.png') }}">
    <meta name="theme-color" content="#1a90d6">

    <!-- Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Axios for AJAX -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* FotoQu Brand - centralized palette */
        :root {
            /* Official brand palette */
            --brand-teal: #053a63;
            /* Teal Blue */
            --brand-orange: #f29223;
            /* Carrot Orange */
            --brand-curious: #1a90d6;
            /* Curious Blue */
            --brand-dodger: #1fa8f0;
            /* Dodger Blue */

            /* Back-compat variables used across existing views */
            --brand-blue: var(--brand-curious);
            --brand-blue-dark: #187db8;
            --brand-blue-100: #e6f4fb;
            --brand-orange-100: #fff2df;
        }

        /* Minimal utility mapping (kept for back-compat) */
        .bg-navy {
            background-color: var(--brand-blue);
        }

        .bg-navy-light {
            background-color: var(--brand-blue-dark);
        }

        .bg-green {
            background-color: var(--brand-orange);
        }

        .bg-green-light {
            background-color: #f7b15d;
        }

        .text-navy {
            color: var(--brand-blue);
        }

        .text-green {
            color: var(--brand-orange);
        }

        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .enhanced-shadow {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .glassmorphism {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        .navy-100 {
            background-color: var(--brand-blue-100);
        }

        .green-100 {
            background-color: var(--brand-orange-100);
        }

        .sidebar-navy {
            background-color: var(--brand-blue);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.15);
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(5, end) infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '.';
            }

            40% {
                content: '..';
            }

            60% {
                content: '...';
            }

            80%,
            100% {
                content: '';
            }
        }

        .notification-badge {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        /* Shared input focus styling (used by multiple admin pages) */
        .input-brand:focus,
        .input-palette:focus {
            outline: none;
            border-color: var(--brand-dodger) !important;
            box-shadow: 0 0 0 3px rgba(31, 168, 240, 0.25) !important;
        }

        /* Shared button styles used in redesigned pages */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-radius: .5rem;
            transition: transform .15s ease, box-shadow .2s, background-color .2s;
        }

        .btn-primary {
            background: var(--brand-teal);
            color: #fff;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 18px rgba(5, 58, 99, .18);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--brand-curious);
            color: #fff;
        }

        .btn-secondary:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 18px rgba(26, 144, 214, .18);
            transform: translateY(-1px);
        }

        .btn-accent {
            background: var(--brand-orange);
            color: #fff;
        }

        .btn-accent:hover {
            filter: brightness(1.05);
            box-shadow: 0 6px 18px rgba(242, 146, 35, .18);
            transform: translateY(-1px);
        }

        .btn-copied {
            background: #16a34a !important;
            color: #fff !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen" data-brand-version="1">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, mobileOpen: false }">
        <!-- Sidebar Overlay -->
        <div x-show="mobileOpen" @click="mobileOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        @include('admin.components.sidebar')


        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header id="adminTopHeader" class="bg-white shadow-sm border-b border-gray-100">
                <div class="px-4 py-3 lg:px-6 lg:py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <!-- Mobile Menu Button -->
                            <button @click="mobileOpen = !mobileOpen"
                                class="text-gray-500 focus:outline-none lg:hidden mr-4">
                                <i class="fas fa-bars text-2xl"></i>
                            </button>

                            <!-- Desktop Menu Button -->
                            <button @click="sidebarOpen = !sidebarOpen"
                                class="text-gray-500 focus:outline-none hidden lg:block mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-bars text-xl"></i>
                            </button>

                            <div>
                                <h2 class="text-xl lg:text-2xl font-bold text-gray-800 line-clamp-1">
                                    @yield('header', 'Dashboard')</h2>
                                <p class="text-gray-600 text-sm mt-1 hidden sm:block">
                                    @yield('description', 'Kelola sistem photobox FotoQu')</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Quick Actions Dropdown (Hidden on Mobile) -->
                            <div class="relative hidden sm:block" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center px-4 py-2 bg-navy text-white rounded-xl hover:bg-navy-light transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <i class="fas fa-plus mr-2"></i>
                                    <span class="font-medium">Quick Action</span>
                                    <i class="fas fa-chevron-down ml-2 text-sm"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                                    <div class="p-2">
                                        <a href="{{ route('admin.sessions.create') }}"
                                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="fas fa-camera w-5 text-navy mr-3"></i>
                                            <div>
                                                <p class="font-medium">Sesi Foto Baru</p>
                                                <p class="text-xs text-gray-500">Buat sesi foto untuk customer</p>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.photoboxes.create') }}"
                                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-lg transition-colors">
                                            <i class="fas fa-cube w-5 text-green mr-3"></i>
                                            <div>
                                                <p class="font-medium">Tambah Photobox</p>
                                                <p class="text-xs text-gray-500">Setup photobox baru</p>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.packages.create') }}"
                                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            <i class="fas fa-box-open w-5 text-navy mr-3"></i>
                                            <div>
                                                <p class="font-medium">Paket Baru</p>
                                                <p class="text-xs text-gray-500">Buat paket harga baru</p>
                                            </div>
                                        </a>
                                        <a href="{{ route('admin.frame-templates.create') }}"
                                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-lg transition-colors">
                                            <i class="fas fa-palette w-5 text-green mr-3"></i>
                                            <div>
                                                <p class="font-medium">Template Frame</p>
                                                <p class="text-xs text-gray-500">Buat template frame baru</p>
                                            </div>
                                        </a>
                                        <a href="#"
                                            class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 rounded-lg transition-colors">
                                            <i class="fas fa-chart-bar w-5 text-green mr-3"></i>
                                            <div>
                                                <p class="font-medium">Export Laporan</p>
                                                <p class="text-xs text-gray-500">Download laporan harian</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Search with Live Results (Responsive) -->
                            <div class="relative" x-data="{ openResults: false, mobileSearchOpen: false, search: '' }">
                                <!-- Desktop Input -->
                                <div class="hidden md:block relative">
                                    <input type="text" x-model="search" @focus="openResults = true"
                                        @input="openResults = search.length > 2; if(search.length > 2) { performLiveSearch(search); }"
                                        placeholder="Cari sesi..."
                                        class="w-48 lg:w-80 pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 text-sm">
                                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                                </div>

                                <!-- Mobile Icon -->
                                <button @click="mobileSearchOpen = !mobileSearchOpen"
                                    class="md:hidden p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <i class="fas fa-search text-xl"></i>
                                </button>

                                <!-- Mobile Search Overlay input -->
                                <div x-show="mobileSearchOpen" @click.away="mobileSearchOpen = false" x-transition
                                    class="absolute right-0 top-12 w-64 bg-white p-2 shadow-xl rounded-xl border border-gray-100 z-50 md:hidden">
                                    <input type="text" x-model="search" x-ref="mobileInput"
                                        @input="openResults = search.length > 2; if(search.length > 2) { performLiveSearch(search); }"
                                        placeholder="Cari sesi, customer..."
                                        class="w-full pl-3 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                </div>

                                <!-- Live Search Results Dropdown -->
                                <div x-show="openResults && search.length > 2" @click.away="openResults = false"
                                    x-transition
                                    class="absolute top-full right-0 mt-2 w-72 md:w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 max-h-80 overflow-y-auto">
                                    <div class="p-3">
                                        <div class="text-xs text-gray-500 mb-2">Hasil pencarian untuk "<span
                                                x-text="search"></span>"</div>
                                        <!-- Search results will be populated via AJAX -->
                                        <div id="search-results" class="space-y-2">
                                            <div class="text-center py-4 text-gray-500">
                                                <i class="fas fa-search text-2xl mb-2"></i>
                                                <p>Ketik minimal 3 karakter untuk mencari</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications with Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" data-notification-button
                                    class="relative p-3 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center notification-badge font-bold"
                                        style="display: none;">0</span>
                                </button>

                                <!-- Notifications Dropdown -->
                                <div x-show="open" @click.away="open = false" x-transition
                                    class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                                    <div class="p-4 border-b border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                                            <button onclick="markAllNotificationsAsRead()"
                                                class="text-sm text-blue-600 hover:text-blue-700">Tandai semua
                                                dibaca</button>
                                        </div>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto" id="notifications-container">
                                        <div class="p-6 text-center text-gray-500">
                                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                            <p>Memuat notifikasi...</p>
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-100">
                                        <button onclick="markAllAsRead()"
                                            class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                            <i class="fas fa-check mr-1"></i>
                                            Tandai semua dibaca
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- User Profile -->
                            <div class="relative">
                                <button onclick="toggleProfileDropdown()"
                                    class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg p-2 transition-colors duration-200">
                                    <div
                                        class="w-10 h-10 bg-blue-900 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">Admin</p>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                                </button>

                                <!-- Profile Dropdown -->
                                <div id="profile-dropdown"
                                    class="hidden absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                                    <div class="py-2">
                                        <a href="{{ route('admin.profile.index') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <i class="fas fa-user mr-3 text-gray-400"></i>
                                            Profil Saya
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-sign-out-alt mr-3 text-red-400"></i>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-6">
                    <!-- Alert Messages -->
                    @if(session('success'))
                        <div
                            class="mb-6 bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Bump data-brand-version to force browser to re-evaluate CSS variables after deploy
        (function () {
            try {
                const el = document.body;
                const v = parseInt(el.getAttribute('data-brand-version') || '1', 10) + 1;
                el.setAttribute('data-brand-version', String(v));
            } catch (e) { }
        })();
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert, [class*="bg-green-"], [class*="bg-red-"]');
            alerts.forEach(alert => {
                if (alert.textContent.includes('success') || alert.textContent.includes('error')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // CSRF token for AJAX requests
        // Reusable Confirm Modal
        (function () {
            const modalId = 'confirm-modal-root';
            if (!document.getElementById(modalId)) {
                const wrap = document.createElement('div');
                wrap.id = modalId;
                wrap.className = 'fixed inset-0 hidden z-[9998]';
                wrap.innerHTML = `
                <div class="absolute inset-0 bg-black/60"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" style="background: rgba(26,144,214,.1); color:#1a90d6"><i class="fas fa-question"></i></div>
                            <div class="text-lg font-semibold text-gray-800" data-cf-title>Konfirmasi</div>
                        </div>
                        <div class="px-5 py-4 text-gray-600" data-cf-message>Anda yakin?</div>
                        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="button" data-cf-cancel class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100">Batal</button>
                            <button type="button" data-cf-confirm class="px-4 py-2 rounded-lg text-white" style="background:#f43f5e">Ya, Lanjutkan</button>
                        </div>
                    </div>
                </div>`;
                document.body.appendChild(wrap);
            }

            window.showConfirmModal = function ({ title = 'Konfirmasi', message = 'Anda yakin?', confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', theme = 'danger' } = {}) {
                return new Promise((resolve) => {
                    const root = document.getElementById(modalId);
                    const elTitle = root.querySelector('[data-cf-title]');
                    const elMessage = root.querySelector('[data-cf-message]');
                    const btnCancel = root.querySelector('[data-cf-cancel]');
                    const btnConfirm = root.querySelector('[data-cf-confirm]');
                    elTitle.textContent = title;
                    elMessage.textContent = message;
                    btnCancel.textContent = cancelText;
                    btnConfirm.textContent = confirmText;
                    // Theme color
                    btnConfirm.style.background = theme === 'danger' ? '#ef4444' : (theme === 'primary' ? '#1a90d6' : '#16a34a');
                    root.classList.remove('hidden');
                    const close = (val) => { root.classList.add('hidden'); resolve(val); };
                    const onKey = (e) => { if (e.key === 'Escape') { cleanup(); close(false); } };
                    const onBackdrop = (e) => { if (e.target === root) { cleanup(); close(false); } };
                    function cleanup() {
                        document.removeEventListener('keydown', onKey);
                        root.removeEventListener('click', onBackdrop);
                        btnCancel.removeEventListener('click', onCancel);
                        btnConfirm.removeEventListener('click', onConfirm);
                    }
                    function onCancel() { cleanup(); close(false); }
                    function onConfirm() { cleanup(); close(true); }
                    document.addEventListener('keydown', onKey);
                    root.addEventListener('click', onBackdrop);
                    btnCancel.addEventListener('click', onCancel);
                    btnConfirm.addEventListener('click', onConfirm);
                });
            }
        })();
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Utility function for CSRF-protected AJAX requests
        window.ajaxRequest = async function (method, url, data = {}, options = {}) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page.');
            }

            const defaultHeaders = {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            };

            const config = {
                method: method.toLowerCase(),
                url: url,
                headers: { ...defaultHeaders, ...(options.headers || {}) },
                ...options
            };

            if (['post', 'put', 'patch'].includes(config.method)) {
                config.data = data;
            }

            try {
                return await axios(config);
            } catch (error) {
                if (error.response && error.response.status === 419) {
                    alert('Sesi telah berakhir. Halaman akan di-refresh otomatis.');
                    setTimeout(() => window.location.reload(), 2000);
                }
                throw error;
            }
        };

        // Live Search functionality
        let searchTimeout;
        function performLiveSearch(query) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (query.length < 3) return;

                axios.post('/admin/search', { query: query })
                    .then(response => {
                        const resultsContainer = document.getElementById('search-results');
                        if (response.data.length === 0) {
                            resultsContainer.innerHTML = `
                                <div class="text-center py-4 text-gray-500">
                                    <i class="fas fa-search text-2xl mb-2"></i>
                                    <p>Tidak ada hasil ditemukan</p>
                                </div>
                            `;
                        } else {
                            resultsContainer.innerHTML = response.data.map(item => `
                                <a href="${item.url}" class="block p-3 hover:bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas ${item.icon} text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">${item.title}</p>
                                            <p class="text-xs text-gray-500">${item.subtitle}</p>
                                        </div>
                                    </div>
                                </a>
                            `).join('');
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                    });
            }, 300);
        }

        // Real-time updates
        function updateDashboardStats() {
            axios.get('/admin/dashboard/stats')
                .then(response => {
                    // Update stat cards
                    Object.keys(response.data).forEach(key => {
                        const element = document.querySelector(`[data-stat="${key}"]`);
                        if (element) {
                            element.textContent = response.data[key];
                        }
                    });
                })
                .catch(error => {
                    console.error('Stats update error:', error);
                });
        }

        // Update notification badge
        function updateNotificationBadge() {
            axios.get('/admin/notifications/unread-count')
                .then(response => {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const count = response.data.count;
                        badge.textContent = count;
                        badge.style.display = count > 0 ? 'flex' : 'none';
                    }
                })
                .catch(error => {
                    console.error('Notification count error:', error);
                });
        }

        // Load notifications in dropdown (simple version that works)
        function loadNotifications() {
            const container = document.querySelector('#notifications-container');
            if (!container) return;

            // Show loading
            container.innerHTML = `
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p>Memuat notifikasi...</p>
                </div>
            `;

            fetch('/admin/notifications')
                .then(response => response.json())
                .then(data => {
                    if (data.notifications && data.notifications.length > 0) {
                        let notificationsHtml = '';

                        data.notifications.forEach(notification => {
                            const timeAgo = formatTimeAgo(notification.time);
                            const readClass = notification.read ? 'bg-gray-50' : 'bg-blue-50 border-l-4 border-l-blue-500';

                            notificationsHtml += `
                                <div class="p-4 hover:bg-gray-50 transition-colors ${readClass} cursor-pointer" 
                                     onclick="handleNotificationClick('${notification.id}', '${notification.action_url || '#'}')">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            ${getNotificationIcon(notification.type)}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                            <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                                            <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                                        </div>
                                        ${!notification.read ? '<div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>' : ''}
                                    </div>
                                </div>
                            `;
                        });

                        container.innerHTML = notificationsHtml;
                    } else {
                        container.innerHTML = `
                            <div class="p-6 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-3xl mb-2 text-gray-400"></i>
                                <p class="text-sm">Tidak ada notifikasi</p>
                                <p class="text-xs mt-1">Semua notifikasi sudah dibaca</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Load notifications error:', error);
                    container.innerHTML = `
                        <div class="p-6 text-center text-red-500">
                            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                            <p class="text-sm">Gagal memuat notifikasi</p>
                        </div>
                    `;
                });
        }

        function getNotificationIcon(type) {
            const icons = {
                'pending_session': '<i class="fas fa-clock text-yellow-500"></i>',
                'completed_session': '<i class="fas fa-check-circle text-green-500"></i>',
                'photobox_update': '<i class="fas fa-camera text-blue-500"></i>'
            };
            return icons[type] || '<i class="fas fa-bell text-gray-500"></i>';
        }

        // Simple mark all as read function
        function markAllAsRead() {
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge();
                        loadNotifications();
                    }
                })
                .catch(error => console.error('Mark all as read error:', error));
        }

        // Alias for the button in dropdown
        function markAllNotificationsAsRead() {
            markAllAsRead();
        }

        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));

            if (diffInMinutes < 1) return 'Baru saja';
            if (diffInMinutes < 60) return `${diffInMinutes} menit lalu`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours} jam lalu`;

            const diffInDays = Math.floor(diffInHours / 24);
            return `${diffInDays} hari lalu`;
        }

        // Handle notification click with proper navigation
        function handleNotificationClick(notificationId, actionUrl) {
            // Mark as read first
            if (notificationId) {
                fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateNotificationBadge();
                            loadNotifications();
                        }
                    })
                    .catch(error => console.error('Mark as read error:', error));
            }

            // Navigate to action URL if provided
            if (actionUrl && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        }

        // Update stats every 30 seconds
        setInterval(updateDashboardStats, 30000);

        // Update notifications every 15 seconds
        setInterval(updateNotificationBadge, 15000);

        // Load notifications when page loads
        updateNotificationBadge();

        // Load notifications when dropdown opens
        document.addEventListener('DOMContentLoaded', function () {
            const notificationButton = document.querySelector('[data-notification-button]');
            if (notificationButton) {
                notificationButton.addEventListener('click', function () {
                    setTimeout(loadNotifications, 100);
                });
            }
        });

        // Profile dropdown functionality
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close profile dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('profile-dropdown');
            const button = event.target.closest('[onclick="toggleProfileDropdown()"]');

            if (!button && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')

    <style>
        .toast {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
            min-width: 280px;
            max-width: 420px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: .75rem 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            pointer-events: auto;
        }

        .toast-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #166534;
        }

        .toast-error {
            border-color: #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .toast-info {
            border-color: #bae6fd;
            background: #eff6ff;
            color: #1e3a8a;
        }

        .toast-title {
            font-weight: 700;
            font-size: .95rem;
        }

        .toast-msg {
            font-size: .85rem;
            opacity: .95;
        }

        .toast-close {
            color: inherit;
            opacity: .7;
        }

        .toast-close:hover {
            opacity: 1;
        }
    </style>
    <script>
        window.showToast = function ({ title = 'Berhasil', message = '', type = 'success', timeout = 2400 } = {}) {
            const root = document.getElementById('toast-root');
            if (!root) return;
            const wrapper = document.createElement('div');
            wrapper.className = `toast toast-${type}`;
            wrapper.innerHTML = `
            <div class="pt-0.5">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : '<i class="fas fa-info-circle"></i>'}
            </div>
            <div class="flex-1">
                <div class="toast-title">${title}</div>
                ${message ? `<div class="toast-msg">${message}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Tutup" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        `;
            root.appendChild(wrapper);
            setTimeout(() => { wrapper.remove(); }, timeout);
        }

        // Ensure toast appears below navbar
        function positionToastsBelowNavbar() {
            const header = document.getElementById('adminTopHeader');
            const root = document.getElementById('toast-root');
            if (!header || !root) return;
            const rect = header.getBoundingClientRect();
            // Header is at top; add small margin
            root.style.top = (rect.height + 12) + 'px';
        }
        window.addEventListener('load', positionToastsBelowNavbar);
        window.addEventListener('resize', positionToastsBelowNavbar);
    </script>
</body>

</html>