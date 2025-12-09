@extends('admin.layout')

@section('title', 'Template Frame')
@section('header', 'Template Frame')
@section('description', 'Kelola semua template frame untuk photobox.')

@section('content')
    <!-- Ensure axios is configured with CSRF token -->
    <script>
        // Configure axios defaults for CSRF protection
        document.addEventListener('DOMContentLoaded', function () {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
        });
    </script>

    {{-- CSS tidak diubah, karena desain visual sudah sesuai --}}
    <style>
        :root {
            --studio-bg: #f4f7fa;
            --sidebar-bg: #ffffff;
            --workspace-bg: #ffffff;
            --border-color: #e2e8f0;
            --navy-color: #1e3a8a;
            --green-color: #059669;
            --text-primary: #1a202c;
            --text-secondary: #718096;
        }

        .design-studio-layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 1.5rem;
            background-color: var(--studio-bg);
            padding: 1.5rem;
            border-radius: 1rem;
        }

        .studio-sidebar {
            background-color: var(--sidebar-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            align-self: start;
        }

        .filter-group {
            margin-bottom: 1.5rem;
        }

        .filter-group-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .filter-link {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
            color: var(--text-primary);
            transition: background-color 0.2s, color 0.2s;
            cursor: pointer;
        }

        .filter-link:hover {
            background-color: #f1f5f9;
        }

        .filter-link.active {
            background-color: var(--navy-color);
            color: white;
            font-weight: 600;
        }

        .filter-link i {
            margin-right: 0.75rem;
            width: 16px;
            text-align: center;
            color: var(--text-secondary);
        }

        .filter-link.active i {
            color: white;
        }

        .studio-workspace {
            min-width: 0;
        }

        .workspace-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .workspace-header .search-bar input {
            width: 300px;
            padding: 0.6rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            background-color: white;
        }

        .template-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -4px rgba(0, 0, 0, 0.07);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(30, 58, 138, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-box {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            text-align: center;
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-box {
            transform: scale(1);
        }
    </style>

    <div x-data="frameTemplatesManager()" class="space-y-8">
        <!-- Header Section -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Studio Frame</h1>
                <p class="text-gray-500 text-sm mt-1">Kelola koleksi desain frame untuk photobox</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative w-full md:w-64">
                    <input type="text" x-model.debounce.300ms="filters.search" placeholder="Cari template..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-xs"></i>
                </div>
                <a href="{{ route('admin.frame-templates.create') }}"
                    class="inline-flex items-center px-5 py-2 bg-blue-600 text-white font-semibold rounded-xl text-sm shadow-md hover:bg-blue-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i> Buat Baru
                </a>
            </div>
        </div>

        <!-- Filters & Stats Bar -->
        <div
            class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white/50 backdrop-blur-sm p-2 rounded-xl border border-gray-200/50">
            <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto p-1">
                <button @click="resetFilters()"
                    :class="filters.status === '' && filters.slots === '' ? 'bg-gray-800 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    Semua
                </button>
                <button @click="applyFilter('status', 'active')"
                    :class="filters.status === 'active' ? 'bg-green-600 text-white shadow-md' : 'text-gray-600 hover:bg-green-50 hover:text-green-700'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    <i class="fas fa-check-circle mr-1.5 text-xs opacity-70"></i>Aktif
                </button>
                <button @click="applyFilter('slots', '6')"
                    :class="filters.slots === '6' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 hover:bg-purple-50 hover:text-purple-700'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap">
                    <i class="fas fa-th mr-1.5 text-xs opacity-70"></i> 6 Slot
                </button>
            </div>

            <div class="flex items-center gap-2 px-3 text-sm text-gray-500 font-medium whitespace-nowrap">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Total <span x-text="'{{ $templates->count() }}'"></span> Template
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($templates as $template)
                <div class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:shadow-xl hover:border-blue-200 transition-all duration-300 flex flex-col h-full"
                    x-show="matchesFilters({{ json_encode($template->only(['id', 'name', 'status', 'slots', 'width', 'height', 'is_default'])) }})"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100">

                    <!-- Preview Image Area -->
                    <div class="relative aspect-[3/4] bg-gray-100 overflow-hidden group-hover:shadow-inner">
                        <img src="{{ $template->preview_url }}" alt="{{ $template->name }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                        <!-- Overlay Gradient -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-black/30 opacity-60 group-hover:opacity-40 transition-opacity">
                        </div>

                        <!-- Top Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2 z-10">
                            @if($template->is_default)
                                <span
                                    class="px-2 py-1 bg-yellow-400 text-yellow-900 text-[10px] font-bold uppercase tracking-wide rounded-md shadow-sm">
                                    <i class="fas fa-star mr-1"></i> Default
                                </span>
                            @endif
                            <span
                                class="px-2 py-1 text-[10px] font-bold uppercase tracking-wide rounded-md shadow-sm {{ $template->status === 'active' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                {{ $template->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <!-- Action Buttons (Hover) -->
                        <div
                            class="absolute inset-0 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 backdrop-blur-[2px]">
                            <a href="{{ route('admin.frame-templates.edit', $template) }}"
                                class="w-10 h-10 flex items-center justify-center bg-white text-gray-700 rounded-full hover:bg-blue-600 hover:text-white hover:scale-110 transition-all shadow-lg"
                                title="Edit Template">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button @click="toggleStatus({{ $template->id }}, '{{ $template->status }}')"
                                class="w-10 h-10 flex items-center justify-center bg-white text-gray-700 rounded-full hover:scale-110 transition-all shadow-lg {{ $template->status === 'active' ? 'hover:bg-gray-800 hover:text-white' : 'hover:bg-green-600 hover:text-white' }}"
                                title="Toggle Status">
                                <i class="fas fa-power-off"></i>
                            </button>
                            <button @click="confirmDelete({{ $template->id }}, '{{ e($template->name) }}')"
                                class="w-10 h-10 flex items-center justify-center bg-white text-gray-700 rounded-full hover:bg-red-500 hover:text-white hover:scale-110 transition-all shadow-lg"
                                title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Bottom Info Overlay -->
                        <div
                            class="absolute bottom-0 left-0 right-0 p-4 transform translate-y-2 group-hover:translate-y-0 transition-transform">
                            <h3 class="text-white font-bold text-lg truncate shadow-black drop-shadow-md">{{ $template->name }}
                            </h3>
                            <div class="flex items-center justify-between text-xs text-gray-200 mt-1">
                                <span
                                    class="bg-black/30 px-2 py-0.5 rounded backdrop-blur-md">{{ $template->width }}x{{ $template->height }}px</span>
                                <span class="bg-black/30 px-2 py-0.5 rounded backdrop-blur-md"><i
                                        class="fas fa-th-large mr-1"></i>{{ $template->slots }} Slots</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 rounded-full mb-4 border border-gray-100">
                        <i class="fas fa-layer-group text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada template</h3>
                    <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">Mulai dengan membuat template frame baru untuk
                        photobox Anda.</p>
                    <a href="{{ route('admin.frame-templates.create') }}"
                        class="inline-flex items-center px-4 py-2 mt-4 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors">
                        <i class="fas fa-plus mr-2"></i> Buat Template
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Confirm Dialog -->
        <div x-show="confirmingDelete" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="confirmingDelete" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="confirmingDelete" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Hapus Template</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus template <strong
                                            x-text="templateToDelete.name"></strong>? Data yang dihapus tidak dapat
                                        dikembalikan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="proceedWithDelete()" type="button"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Ya, Hapus
                        </button>
                        <button @click="cancelDelete()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT DENGAN PERBAIKAN FUNGSIONALITAS FILTER --}}
    <script>
        function frameTemplatesManager() {
            return {
                viewMode: 'grid',
                filters: Alpine.reactive({
                    slots: '',
                    status: '',
                    search: ''
                }),
                // Toast state
                toasts: [],
                toastId: 0,
                pushToast(type, message, title = null) {
                    const id = ++this.toastId;
                    this.toasts.push({ id, type, message, title });
                    setTimeout(() => this.removeToast(id), 3000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },
                confirmingDelete: false,
                templateToDelete: {},

                // PERBAIKAN: Fungsi ini dipanggil secara eksplisit untuk mengubah filter
                applyFilter(type, value) {
                    this.filters[type] = value;
                    // Force Alpine.js to re-evaluate by triggering change detection
                    this.$nextTick(() => {
                        // Console log untuk debugging
                        console.log('Filter applied:', type, value, this.filters);
                    });
                },

                matchesFilters(template) {
                    // Debugging logs
                    console.log('Checking template:', template.name, 'with filters:', this.filters);

                    const searchMatch = this.filters.search === '' ||
                        template.name.toLowerCase().includes(this.filters.search.toLowerCase());

                    const statusMatch = this.filters.status === '' ||
                        template.status === this.filters.status;

                    // Convert both to strings for comparison
                    const slotsMatch = this.filters.slots === '' ||
                        String(template.slots) === String(this.filters.slots);

                    const result = searchMatch && statusMatch && slotsMatch;

                    console.log('Template', template.name, '- search:', searchMatch, 'status:', statusMatch, 'slots:', slotsMatch, 'result:', result);

                    return result;
                },

                // TAMBAHAN: Method untuk reset semua filter
                resetFilters() {
                    this.filters.slots = '';
                    this.filters.status = '';
                    this.filters.search = '';
                },

                // --- Modal Logic (tetap sama) ---
                confirmDelete(id, name) {
                    this.templateToDelete = { id, name };
                    this.confirmingDelete = true;
                },
                cancelDelete() {
                    this.confirmingDelete = false;
                },
                proceedWithDelete() {
                    this.deleteTemplate(this.templateToDelete.id);
                    this.confirmingDelete = false;
                },

                // --- Original Functions (dengan CSRF protection) ---
                async toggleStatus(templateId, currentStatus) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            throw new Error('CSRF token not found. Please refresh the page.');
                        }

                        const response = await axios.post(`/admin/frame-templates/${templateId}/toggle-status`, {}, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.data && response.data.message) {
                            this.pushToast('success', response.data.message);
                        } else {
                            this.pushToast('success', 'Status template berhasil diubah');
                        }
                        window.location.reload();
                    } catch (error) {
                        console.error('Failed to toggle status:', error);
                        let errorMessage = 'Gagal mengubah status template.';

                        if (error.response && error.response.status === 419) {
                            errorMessage = 'Sesi telah berakhir. Silakan refresh halaman dan coba lagi.';
                            setTimeout(() => window.location.reload(), 2000);
                        } else if (error.response && error.response.data && error.response.data.error) {
                            errorMessage = error.response.data.error;
                        } else if (error.message && error.message.includes('CSRF token not found')) {
                            errorMessage = error.message;
                        }

                        this.pushToast('error', errorMessage);
                    }
                },
                async setDefault(templateId) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            throw new Error('CSRF token not found. Please refresh the page.');
                        }

                        const response = await axios.post(`/admin/frame-templates/${templateId}/set-default`, {}, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.data && response.data.message) {
                            this.pushToast('success', response.data.message);
                        } else {
                            this.pushToast('success', 'Berhasil menjadikan default');
                        }
                        window.location.reload();
                    } catch (error) {
                        console.error('Failed to set default:', error);
                        let errorMessage = 'Gagal menjadikan template default.';

                        if (error.response && error.response.status === 419) {
                            errorMessage = 'Sesi telah berakhir. Silakan refresh halaman dan coba lagi.';
                            setTimeout(() => window.location.reload(), 2000);
                        } else if (error.response && error.response.data && error.response.data.error) {
                            errorMessage = error.response.data.error;
                        } else if (error.message && error.message.includes('CSRF token not found')) {
                            errorMessage = error.message;
                        }

                        this.pushToast('error', errorMessage);
                    }
                },
                async deleteTemplate(templateId) {
                    try {
                        // Ensure CSRF token is available
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            throw new Error('CSRF token not found. Please refresh the page.');
                        }

                        // Set axios default headers if not already set
                        if (!axios.defaults.headers.common['X-CSRF-TOKEN']) {
                            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
                        }

                        const response = await axios.delete(`/admin/frame-templates/${templateId}`, {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.data && response.data.success) {
                            this.pushToast('success', response.data.success);
                        } else {
                            this.pushToast('success', 'Template berhasil dihapus.');
                        }
                        window.location.reload();
                    } catch (error) {
                        console.error('Failed to delete template:', error);
                        let errorMessage = 'Gagal menghapus template.';

                        if (error.response && error.response.status === 419) {
                            errorMessage = 'Sesi telah berakhir. Silakan refresh halaman dan coba lagi.';
                            // Auto refresh page after 2 seconds for 419 errors
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else if (error.response && error.response.data && error.response.data.error) {
                            errorMessage = error.response.data.error;
                        } else if (error.response && error.response.status === 404) {
                            errorMessage = 'Template tidak ditemukan.';
                        } else if (error.response && error.response.data && error.response.data.message) {
                            errorMessage = error.response.data.message;
                        } else if (error.message && error.message.includes('CSRF token not found')) {
                            errorMessage = error.message;
                        }

                        this.pushToast('error', errorMessage);
                    }
                },
            }
        }
    </script>
@endsection