@extends('admin.layout')

@section('header', 'Manajemen Fotobooth')
@section('description', 'Kelola dan pantau semua Fotobooth dalam sistem')

@section('content')
<style>
    /* Redesign Concept: The "Operational Status Board" */
    :root {
        --page-bg: #f7f9fc;
        --border-color: #e9eef5;
        --navy-color: #1e3a8a;
        --text-primary: #2c3e50;
        --text-secondary: #7f8c8d;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 99px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .filter-nav a {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        color: var(--text-secondary);
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .filter-nav a.active,
    .filter-nav a:hover {
        color: var(--navy-color);
        background-color: #eef2ff;
    }

    .filter-nav a.active {
        border-color: #c7d2fe;
    }

    .action-dropdown {
        position: relative;
    }

    .action-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        z-index: 10;
        width: 170px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .action-menu a,
    .action-menu button {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        transition: background-color 0.2s;
        text-align: left;
    }

    .action-menu a:hover,
    .action-menu button:hover {
        background-color: #f7f9fc;
    }
</style>

<div class="space-y-6">
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <a href="{{ route('admin.photoboxes.create') }}"
                class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-navy-600 text-white font-semibold rounded-xl shadow-md hover:bg-navy-700 transition-all"
                style="background-color: var(--navy-color)">
                <i class="fas fa-plus mr-2"></i>Tambah Fotobooth
            </a>
            <div class="text-sm text-gray-500">
                Total <strong class="text-gray-800">{{ $photoboxes->count() }}</strong> Fotobooth ditemukan
            </div>
        </div>

        <div class="bg-white p-2 rounded-xl border border-gray-200 overflow-x-auto">
            <nav class="filter-nav flex items-center gap-2 min-w-max">
                <a href="{{ route('admin.photoboxes.index', ['status' => 'all']) }}"
                    class="{{ !request('status') || request('status') == 'all' ? 'active' : '' }}">Semua</a>
                <a href="{{ route('admin.photoboxes.index', ['status' => 'active']) }}"
                    class="{{ request('status') == 'active' ? 'active' : '' }}">Aktif</a>
                <a href="{{ route('admin.photoboxes.index', ['status' => 'inactive']) }}"
                    class="{{ request('status') == 'inactive' ? 'active' : '' }}">Tidak Aktif</a>
                <a href="{{ route('admin.photoboxes.index', ['status' => 'maintenance']) }}"
                    class="{{ request('status') == 'maintenance' ? 'active' : '' }}">Maintenance</a>
            </nav>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fotobooth</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lokasi</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sesi Aktif</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($photoboxes as $photobox)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @php
                                    $statusColor = 'bg-gray-400';
                                    if ($photobox->status === 'active')
                                        $statusColor = 'bg-green-500';
                                    else if ($photobox->status === 'inactive')
                                        $statusColor = 'bg-red-500';
                                    else if ($photobox->status === 'maintenance')
                                        $statusColor = 'bg-yellow-500';
                                @endphp
                                <div class="w-2.5 h-2.5 rounded-full {{ $statusColor }} mr-4 flex-shrink-0"></div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $photobox->name }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $photobox->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $photobox->location }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badgeClass = 'bg-gray-100 text-gray-800';
                                $badgeIcon = '⚫';
                                if ($photobox->status === 'active') {
                                    $badgeClass = 'bg-green-100 text-green-800';
                                    $badgeIcon = '🟢';
                                } else if ($photobox->status === 'inactive') {
                                    $badgeClass = 'bg-red-100 text-red-800';
                                    $badgeIcon = '';
                                } else if ($photobox->status === 'maintenance') {
                                    $badgeClass = 'bg-yellow-100 text-yellow-800';
                                    $badgeIcon = '🟡';
                                }
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">
                                {{ $badgeIcon }} {{ ucfirst($photobox->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-800">
                            {{ $photobox->active_photo_sessions_count }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                @php
                                    $activeToken = $photobox->activeAccessToken;
                                @endphp
                                <button
                                    class="inline-flex items-center px-3 py-2 text-sm text-white rounded-lg shadow hover:shadow-md transition bg-[#1a90d6] hover:bg-[#157ab5]"
                                    title="Lihat token akses 24 jam" onclick="openTokenDrawer(this)"
                                    data-photobox-id="{{ $photobox->id }}" data-photobox-name="{{ e($photobox->name) }}"
                                    data-photobox-code="{{ e($photobox->code) }}"
                                    data-generate-url="{{ route('admin.photoboxes.generate-token', $photobox) }}"
                                    data-revoke-url="{{ route('admin.photoboxes.revoke-token', $photobox) }}"
                                    data-active-link-url="{{ $activeToken ? route('photobox.show', ['photobox' => $photobox->code]) . '?token=' . $activeToken->token : '' }}"
                                    data-token="{{ $activeToken ? $activeToken->token : '' }}"
                                    data-expires-at="{{ $activeToken ? $activeToken->expires_at->toIso8601String() : '' }}">
                                    <i class="fas fa-key mr-2"></i> Token
                                </button>
                                <div x-data="{ open: false }" class="action-dropdown relative">
                                    <button @click="open = !open" @click.away="open = false"
                                        class="p-2 text-gray-500 hover:text-gray-800 rounded-full hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-ellipsis-h w-5 h-5 flex items-center justify-center"></i>
                                    </button>
                                    <div x-show="open" x-transition.origin.top.right class="action-menu" x-cloak>
                                        <a href="{{ route('admin.photoboxes.edit', $photobox) }}"><i
                                                class="fas fa-edit w-4"></i> Edit</a>
                                        @if($photobox->status !== 'inactive')
                                            <button onclick="toggleStatus({{ $photobox->id }})">
                                                <i class="fas fa-power-off w-4"></i>
                                                {{ $photobox->status === 'active' ? 'Maintenance' : 'Aktifkan' }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-cube text-gray-300 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-700">Tidak Ada Fotobooth Ditemukan</h3>
                                <p class="text-gray-500 max-w-sm mt-1">Mulai dengan menambahkan Fotobooth pertama Anda
                                    untuk mengelola sesi foto.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4">
        @forelse($photoboxes as $photobox)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100 text-gray-600">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $photobox->name }}</h3>
                        <div
                            class="text-xs font-mono text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded inline-block mt-1">
                            {{ $photobox->code }}
                        </div>
                    </div>
                </div>
                @php
                    $badgeClass = 'bg-gray-100 text-gray-800';
                    if ($photobox->status === 'active')
                        $badgeClass = 'bg-green-100 text-green-800';
                    else if ($photobox->status === 'inactive')
                        $badgeClass = 'bg-red-100 text-red-800';
                    else if ($photobox->status === 'maintenance')
                        $badgeClass = 'bg-yellow-100 text-yellow-800';
                @endphp
                <span class="status-badge {{ $badgeClass }} text-xs">
                    {{ ucfirst($photobox->status) }}
                </span>
            </div>

            <div class="space-y-3 mb-5">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt w-5 text-gray-400 text-center mr-2"></i>
                    {{ $photobox->location }}
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-users w-5 text-gray-400 text-center mr-2"></i>
                    <span><strong class="text-gray-900">{{ $photobox->active_photo_sessions_count }}</strong> Sesi
                        Aktif</span>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                @php
                    $activeToken = $photobox->activeAccessToken;
                @endphp
                <button
                    class="flex-1 inline-flex justify-center items-center px-4 py-2 text-sm text-white rounded-xl shadow-sm hover:shadow active:scale-95 transition-all bg-[#1a90d6]"
                    onclick="openTokenDrawer(this)" data-photobox-id="{{ $photobox->id }}"
                    data-photobox-name="{{ e($photobox->name) }}" data-photobox-code="{{ e($photobox->code) }}"
                    data-generate-url="{{ route('admin.photoboxes.generate-token', $photobox) }}"
                    data-revoke-url="{{ route('admin.photoboxes.revoke-token', $photobox) }}"
                    data-active-link-url="{{ $activeToken ? route('photobox.show', ['photobox' => $photobox->code]) . '?token=' . $activeToken->token : '' }}"
                    data-token="{{ $activeToken ? $activeToken->token : '' }}"
                    data-expires-at="{{ $activeToken ? $activeToken->expires_at->toIso8601String() : '' }}">
                    <i class="fas fa-key mr-2"></i> Token
                </button>

                <a href="{{ route('admin.photoboxes.edit', $photobox) }}"
                    class="inline-flex justify-center items-center p-2.5 text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors">
                    <i class="fas fa-edit"></i>
                </a>

                @if($photobox->status !== 'inactive')
                    <button onclick="toggleStatus({{ $photobox->id }})"
                        class="inline-flex justify-center items-center p-2.5 text-gray-600 bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition-colors"
                        title="{{ $photobox->status === 'active' ? 'Maintenance' : 'Aktifkan' }}">
                        <i class="fas fa-power-off"></i>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-cube text-gray-300 text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700">Tidak Ada Fotobooth</h3>
            <p class="text-gray-500 mt-1">Belum ada data fotobooth.</p>
        </div>
        @endforelse
    </div>

    <!-- Token Drawer: appears below the table when a row's "Lihat Token" is clicked -->
    <div id="tokenDrawer" class="hidden bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100" style="background:#f8fafc">
            <div>
                <div class="text-xs uppercase tracking-wide text-gray-500">Token Akses Desktop</div>
                <div class="text-lg font-semibold text-gray-900" id="td-title">—</div>
                <div class="text-xs text-gray-500 font-mono" id="td-code">—</div>
            </div>
            <div class="flex items-center gap-2">
                <button id="td-close"
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700"><i
                        class="fas fa-times mr-1"></i>Tutup</button>
            </div>
        </div>
        <div class="p-6">
            <!-- When token exists -->
            <div id="td-has-token" class="hidden space-y-3">
                <div class="text-sm text-gray-600">Salin token ini ke pengaturan <b>Aplikasi Desktop FotoQu</b>.</div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <div class="flex-1 flex items-stretch gap-2">
                        <div class="flex-1 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 font-mono text-sm truncate font-bold text-blue-700"
                            id="td-display-token">—</div>
                        <button class="px-3 py-2 rounded-lg text-white" style="background:#1a90d6" id="td-copy"><i
                                class="fas fa-copy mr-1"></i>Salin Token</button>
                    </div>
                    <div class="flex items-center gap-2">
                        <button id="td-revoke"
                            class="px-3 py-2 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 inline-flex items-center"><i
                                class="fas fa-ban mr-2"></i>Cabut</button>
                    </div>
                </div>
                <div class="text-xs text-gray-500" id="td-exp">Berlaku sampai: —</div>
            </div>

            <!-- When no token exists -->
            <div id="td-empty" class="hidden">
                <div class="text-sm text-gray-600 mb-3">Belum ada token aktif. Buat token untuk menghubungkan Desktop
                    App.
                </div>
                <button id="td-generate"
                    class="inline-flex items-center px-4 py-2 text-sm text-white rounded-lg shadow hover:shadow-md"
                    style="background:#1fa8f0"><i class="fas fa-key mr-2"></i>Buat Token Baru</button>
            </div>
        </div>
    </div>
</div>

{{-- All original JavaScript logic is preserved and untouched --}}
@push('scripts')
    <script>
        // Drawer state
        const tokenDrawerEl = document.getElementById('tokenDrawer');
        const tdTitle = document.getElementById('td-title');
        const tdCode = document.getElementById('td-code');
        const tdHas = document.getElementById('td-has-token');
        const tdEmpty = document.getElementById('td-empty');
        const tdDisplayToken = document.getElementById('td-display-token');
        const tdCopy = document.getElementById('td-copy');
        const tdRevoke = document.getElementById('td-revoke');
        const tdExp = document.getElementById('td-exp');
        const tdGenerate = document.getElementById('td-generate');
        const tdClose = document.getElementById('td-close');

        let tdState = {
            photoboxId: null,
            name: '',
            code: '',
            token: '',
            expiresAt: '',
            generateUrl: '',
            revokeUrl: ''
        };

        function renderTokenDrawer() {
            tdTitle.textContent = tdState.name || '—';
            tdCode.textContent = tdState.code ? `Kode: ${tdState.code}` : '—';
            const has = !!tdState.token;
            tdHas.classList.toggle('hidden', !has);
            tdEmpty.classList.toggle('hidden', has);
            if (has) {
                tdDisplayToken.textContent = tdState.token;
                tdExp.textContent = tdState.expiresAt ? `Berlaku sampai: ${new Date(tdState.expiresAt).toLocaleString()}` : '';
            }
            tokenDrawerEl.classList.remove('hidden');
            // Scroll into view to simulate table expanding downwards
            tokenDrawerEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function openTokenDrawer(buttonEl) {
            tdState.photoboxId = buttonEl.getAttribute('data-photobox-id');
            tdState.name = buttonEl.getAttribute('data-photobox-name');
            tdState.code = buttonEl.getAttribute('data-photobox-code');
            tdState.token = buttonEl.getAttribute('data-token') || '';
            tdState.expiresAt = buttonEl.getAttribute('data-expires-at') || '';
            tdState.generateUrl = buttonEl.getAttribute('data-generate-url');
            tdState.revokeUrl = buttonEl.getAttribute('data-revoke-url');
            renderTokenDrawer();
        }

        tdClose?.addEventListener('click', () => {
            tokenDrawerEl.classList.add('hidden');
        });

        tdCopy?.addEventListener('click', async () => {
            if (!tdState.token) return;
            try { await navigator.clipboard.writeText(tdState.token); window.showToast && window.showToast({ title: 'Tersalin', message: 'Token disalin ke clipboard', type: 'success' }); } catch { window.showToast && window.showToast({ title: 'Gagal', message: 'Gagal menyalin token', type: 'error' }); }
        });

        tdGenerate?.addEventListener('click', async () => {
            if (!tdState.generateUrl) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await axios.post(tdState.generateUrl, { _token: csrfToken });
                if (res.data && res.data.success) {
                    // Extract token from URL if not provided directly
                    // Assuming backend returns 'url' like ...?token=XYZ
                    let newToken = '';
                    if (res.data.token) {
                        newToken = res.data.token;
                    } else if (res.data.url) {
                        try {
                            const u = new URL(res.data.url);
                            newToken = u.searchParams.get('token');
                        } catch (e) { }
                    }

                    tdState.token = newToken;
                    tdState.expiresAt = res.data.expires_at;
                    renderTokenDrawer();
                    try { await navigator.clipboard.writeText(tdState.token); window.showToast && window.showToast({ title: 'Token dibuat', message: 'Token disalin ke clipboard', type: 'success' }); } catch { window.showToast && window.showToast({ title: 'Token dibuat', message: 'Token berhasil dibuat', type: 'success' }); }
                } else {
                    window.showToast && window.showToast({ title: 'Gagal', message: res.data.message || 'Gagal membuat token', type: 'error' });
                }
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: (e.response?.data?.message || e.message), type: 'error' });
            }
        });

        tdRevoke?.addEventListener('click', async () => {
            if (!tdState.revokeUrl || !tdState.token) return;
            const ok = await (window.showConfirmModal ? window.showConfirmModal({ title: 'Cabut Token?', message: 'Akses Photobox akan ditolak hingga token baru dibuat.', confirmText: 'Cabut', theme: 'danger' }) : Promise.resolve(confirm('Cabut token ini?')));
            if (!ok) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await axios.post(tdState.revokeUrl, { token: tdState.token, _token: csrfToken });
                if (res.data && res.data.success) {
                    tdState.token = '';
                    tdState.expiresAt = '';
                    renderTokenDrawer();
                    window.showToast && window.showToast({ title: 'Token dicabut', type: 'success' });
                } else {
                    window.showToast && window.showToast({ title: 'Gagal', message: res.data.message || 'Gagal mencabut token', type: 'error' });
                }
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: (e.response?.data?.message || e.message), type: 'error' });
            }
        });

        function refreshData() {
            // Arahkan ke halaman index tanpa filter untuk refresh
            window.location.href = '{{ route('admin.photoboxes.index') }}';
        }

        async function toggleStatus(photoboxId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const response = await axios.post(`/admin/photoboxes/${photoboxId}/toggle-status`, {
                    _token: csrfToken
                });

                if (response.data.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Gagal mengubah status Fotobooth.');
                }
            } catch (error) {
                console.error("Error toggling status:", error);
                alert('Terjadi kesalahan saat mencoba mengubah status Fotobooth.');
            }
        }

        async function generateToken(buttonEl) {
            const endpoint = buttonEl.getAttribute('data-url');
            const code = buttonEl.getAttribute('data-code');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                const res = await axios.post(endpoint, { _token: csrfToken });
                if (res.data && res.data.success) {
                    const resUrl = res.data.url;
                    const expires_at = res.data.expires_at;
                    // If drawer is currently showing this photobox, update drawer; else fallback to alert
                    if (tdState && tdState.generateUrl === endpoint) {
                        tdState.linkUrl = resUrl;
                        tdState.expiresAt = expires_at;
                        renderTokenDrawer();
                        window.showToast && window.showToast({ title: 'Token dibuat', message: 'Link akses disalin ke clipboard', type: 'success' });
                    } else {
                        await navigator.clipboard.writeText(resUrl);
                        window.showToast && window.showToast({ title: 'Token dibuat', message: 'Link akses disalin ke clipboard', type: 'success' });
                    }
                } else {
                    window.showToast && window.showToast({ title: 'Gagal', message: res.data.message || 'Gagal membuat token', type: 'error' });
                }
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: (e.response?.data?.message || e.message), type: 'error' });
            }
        }

        async function copyText(text) {
            try {
                await navigator.clipboard.writeText(text);
                window.showToast && window.showToast({ title: 'Tersalin', message: 'Link disalin ke clipboard', type: 'success' });
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: 'Gagal menyalin link', type: 'error' });
            }
        }

        async function revokeToken(url, token) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const ok = await (window.showConfirmModal ? window.showConfirmModal({ title: 'Cabut Token?', message: 'Akses Photobox akan ditolak hingga token baru dibuat.', confirmText: 'Cabut', theme: 'danger' }) : Promise.resolve(confirm('Cabut token ini?')));
            if (!ok) return;
            try {
                const res = await axios.post(url, { token, _token: csrfToken });
                if (res.data && res.data.success) {
                    location.reload();
                } else {
                    window.showToast && window.showToast({ title: 'Gagal', message: res.data.message || 'Gagal mencabut token', type: 'error' });
                }
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: (e.response?.data?.message || e.message), type: 'error' });
            }
        }
    </script>
@endpush
@endsection