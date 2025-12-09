@extends('admin.layout')

@section('header', 'Daftar Sesi Foto')
@section('description', 'Kelola semua sesi foto photobox')

@section('content')
    <style>
        :root {
            /* Brand palette */
            --brand-teal: #053a63;
            /* Teal Blue */
            --brand-orange: #f29223;
            /* Carrot Orange */
            --brand-curious: #1a90d6;
            /* Curious Blue */
            --brand-dodger: #1fa8f0;
            /* Dodger Blue */
        }

        /* Inputs/selects */
        .input-palette {
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.5rem 0.75rem;
            transition: box-shadow .2s, border-color .2s;
        }

        .input-palette:focus {
            outline: none;
            border-color: var(--brand-curious);
            box-shadow: 0 0 0 3px rgba(26, 144, 214, .2);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: transform .15s ease, box-shadow .2s, background-color .2s;
        }

        .btn-primary {
            background: var(--brand-teal);
            color: #fff;
        }

        .btn-primary:hover {
            filter: brightness(1.05);
            box-shadow: 0 8px 20px rgba(5, 58, 99, .2);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--brand-curious);
            color: #fff;
        }

        .btn-secondary:hover {
            filter: brightness(1.05);
            box-shadow: 0 8px 20px rgba(26, 144, 214, .2);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: #fff;
            color: #475569;
            border: 1px solid #e5e7eb;
        }

        .btn-ghost:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
        }

        .btn-soft-info {
            background: rgba(26, 144, 214, .12);
            color: var(--brand-curious);
        }

        .btn-soft-info:hover {
            background: rgba(26, 144, 214, .18);
        }

        .btn-soft-success {
            background: rgba(5, 58, 99, .12);
            color: var(--brand-teal);
        }

        .btn-soft-success:hover {
            background: rgba(5, 58, 99, .18);
        }

        /* Avatars */
        .avatar-gradient {
            background: linear-gradient(135deg, var(--brand-curious), var(--brand-dodger));
            color: #fff;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 600;
        }

        .badge-created {
            background: rgba(242, 146, 35, .12);
            color: var(--brand-orange);
        }

        .badge-approved {
            background: rgba(26, 144, 214, .12);
            color: var(--brand-curious);
        }

        .badge-inprogress {
            background: rgba(31, 168, 240, .12);
            color: var(--brand-dodger);
        }

        .badge-completed {
            background: rgba(5, 58, 99, .12);
            color: var(--brand-teal);
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-paid {
            background: rgba(5, 58, 99, .12);
            color: var(--brand-teal);
        }

        .badge-pending {
            background: rgba(242, 146, 35, .12);
            color: var(--brand-orange);
        }

        .badge-failed {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* Progress bar */
        .progress-brand {
            background: linear-gradient(90deg, var(--brand-curious), var(--brand-dodger));
        }

        /* Frame ready */
        .frame-ready {
            background: linear-gradient(90deg, rgba(5, 58, 99, .06), rgba(31, 168, 240, .06));
            border: 1px solid rgba(26, 144, 214, .25);
        }

        .frame-ready .title {
            color: var(--brand-teal);
        }

        .frame-ready .printed {
            background: rgba(31, 168, 240, .12);
            color: var(--brand-dodger);
        }

        .price {
            color: var(--brand-curious);
        }
    </style>
    <div class="space-y-6">
        <!-- Action Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary px-6 py-3 shadow-md">
                    <i class="fas fa-plus mr-2"></i>
                    Sesi Baru
                </a>
                <button onclick="refreshData()" class="btn btn-ghost p-3">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            <!-- Filters -->
            <div class="flex items-center space-x-3">
                <form method="GET" class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, email, atau kode..." class="pl-10 pr-4 py-2 input-palette w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>

                    <!-- Status Filter -->
                    <select name="status" class="input-palette px-4 py-2">
                        <option value="">Semua Status</option>
                        <option value="created" {{ request('status') === 'created' ? 'selected' : '' }}>Dibuat</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Berlangsung
                        </option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan
                        </option>
                    </select>

                    <!-- Photobox Filter -->
                    <select name="photobox_id" class="input-palette px-4 py-2">
                        <option value="">Semua Photobox</option>
                        @foreach($photoboxes as $photobox)
                            <option value="{{ $photobox->id }}" {{ request('photobox_id') == $photobox->id ? 'selected' : '' }}>
                                {{ $photobox->code }} - {{ $photobox->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-secondary px-4 py-2">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'status', 'photobox_id']))
                        <a href="{{ route('admin.sessions.index') }}"
                            class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Sessions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($sessions as $session)
                <div
                    class="bg-white rounded-2xl p-6 card-shadow border border-gray-100 hover:shadow-lg transition-all duration-200">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg avatar-gradient">
                                {{ substr($session->customer_name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $session->customer_name }}</h3>
                                <p class="text-sm text-gray-500">{{ $session->session_code }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="badge
                                @if($session->session_status === 'completed') badge-completed
                                @elseif($session->session_status === 'in_progress') badge-inprogress
                                @elseif($session->session_status === 'approved') badge-approved
                                @elseif($session->session_status === 'cancelled') badge-cancelled
                                @else badge-created @endif">
                                @if($session->session_status === 'created') 📝 Dibuat
                                @elseif($session->session_status === 'approved') ✅ Disetujui
                                @elseif($session->session_status === 'in_progress') 🔄 Berlangsung
                                @elseif($session->session_status === 'completed') 🎉 Selesai
                                @elseif($session->session_status === 'cancelled') ❌ Dibatalkan
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="space-y-3 mb-4">
                        <!-- Package Detail -->
                        <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Paket</span>
                                @if($session->total_price == 0)
                                    <span
                                        class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-xs font-bold ring-1 ring-green-200">
                                        <i class="fas fa-gift mr-1"></i> GRATIS
                                    </span>
                                @else
                                    <span
                                        class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold ring-1 ring-blue-100">
                                        BERBAYAR
                                    </span>
                                @endif
                            </div>
                            <div class="font-medium text-gray-800 text-sm">
                                {{ $session->package->name ?? 'Paket Tidak Diketahui' }}
                            </div>
                            @if($session->package)
                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-film mr-1"></i> {{ $session->package->frame_slots }} Slot Foto
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="block text-xs text-gray-500 mb-1">Photobox</span>
                                <span class="font-medium text-gray-800 flex items-center">
                                    <i class="fas fa-box text-gray-400 mr-1.5"></i>
                                    {{ $session->photobox->code }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500 mb-1">Harga</span>
                                @if($session->total_price == 0)
                                    <span class="font-bold text-green-600">Rp 0</span>
                                @else
                                    <span class="font-bold price">Rp {{ number_format($session->total_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-2 border-t border-dashed border-gray-200">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-credit-card mr-2 text-gray-400"></i> Status Bayar:
                                </span>
                                <span class="badge
                                    @if($session->payment_status === 'paid') badge-paid
                                    @elseif($session->payment_status === 'failed') badge-failed
                                    @else badge-pending @endif">
                                    @if($session->payment_status === 'paid')
                                        @if($session->total_price == 0)
                                            ✅ Terverifikasi
                                        @else
                                            ✅ Lunas
                                        @endif
                                    @elseif($session->payment_status === 'failed') ❌ Gagal
                                    @else ⏳ Pending
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-envelope mr-2 text-gray-400"></i> Email:
                                </span>
                                <div class="truncate max-w-[150px] text-right" title="{{ $session->customer_email }}">
                                    @if($session->customer_email)
                                        <span class="font-medium text-gray-800">{{ $session->customer_email }}</span>
                                    @else
                                        <span class="italic text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    @if($session->photos->count() > 0)
                        <div class="mb-4">
                            @php $adminTotalPhotos = config('fotoku.total_photos', 3); @endphp
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-600">Progress Foto:</span>
                                <span class="font-medium">{{ $session->photos->count() }}/{{ $adminTotalPhotos }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                @php
                                    $pct = $adminTotalPhotos > 0 ? ($session->photos->count() / $adminTotalPhotos) * 100 : 0;
                                    $pct = $pct > 100 ? 100 : $pct;
                                @endphp
                                <div class="progress-brand h-2 rounded-full transition-all duration-300" style="width: {{ $pct }}%">
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Frame Preview -->
                    @if($session->frame)
                        <div class="mb-4 p-3 rounded-xl frame-ready">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-check-circle" style="color: var(--brand-teal)"></i>
                                    <span class="text-sm font-medium title">Frame Siap!</span>
                                </div>
                                @if($session->frame->is_printed)
                                    <span class="text-xs printed px-2 py-1 rounded-full">🖨️ Tercetak</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            {{ $session->created_at->diffForHumans() }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.sessions.show', $session) }}"
                                class="inline-flex items-center px-3 py-1.5 btn-soft-info rounded-lg transition-colors text-sm">
                                <i class="fas fa-eye mr-1"></i>
                                Detail
                            </a>

                            @if($session->session_status === 'created' && $session->payment_status === 'paid')
                                <form method="POST" action="{{ route('admin.sessions.approve', $session) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 btn-soft-success rounded-lg transition-colors text-sm"
                                        onclick="return confirm('Setujui sesi ini?')">
                                        <i class="fas fa-check mr-1"></i>
                                        Setujui
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl p-12 text-center card-shadow border border-gray-100">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-camera text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Belum Ada Sesi Foto</h3>
                        <p class="text-gray-600 mb-6">Mulai dengan membuat sesi foto baru untuk customer.</p>
                        <a href="{{ route('admin.sessions.create') }}" class="btn btn-primary px-6 py-3 shadow-md">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Sesi Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($sessions->hasPages())
            <div class="flex justify-center">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3">
                    <nav class="flex items-center space-x-2" aria-label="Pagination">
                        {{-- Previous --}}
                        @if($sessions->onFirstPage())
                            <span class="px-3 py-1.5 text-sm text-gray-400 rounded-lg border">&larr; Prev</span>
                        @else
                            <a href="{{ $sessions->previousPageUrl() }}"
                                class="px-3 py-1.5 text-sm bg-white hover:bg-gray-100 rounded-lg border">&larr; Prev</a>
                        @endif

                        {{-- Page links (windowed) --}}
                        @php
                            $from = max(1, $sessions->currentPage() - 2);
                            $to = min($sessions->lastPage(), $sessions->currentPage() + 2);
                        @endphp
                        @if($from > 1)
                            <a href="{{ $sessions->url(1) }}" class="px-3 py-1.5 text-sm rounded-lg border hover:bg-gray-50">1</a>
                            @if($from > 2)
                                <span class="px-2 text-sm text-gray-400">…</span>
                            @endif
                        @endif

                        @for($page = $from; $page <= $to; $page++)
                            @if($page == $sessions->currentPage())
                                <span aria-current="page"
                                    class="px-3 py-1.5 bg-blue-500 text-white rounded-lg text-sm font-semibold">{{ $page }}</span>
                            @else
                                <a href="{{ $sessions->url($page) }}"
                                    class="px-3 py-1.5 text-sm rounded-lg border hover:bg-gray-50">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($to < $sessions->lastPage())
                            @if($to < $sessions->lastPage() - 1)
                                <span class="px-2 text-sm text-gray-400">…</span>
                            @endif
                            <a href="{{ $sessions->url($sessions->lastPage()) }}"
                                class="px-3 py-1.5 text-sm rounded-lg border hover:bg-gray-50">{{ $sessions->lastPage() }}</a>
                        @endif

                        {{-- Next --}}
                        @if($sessions->hasMorePages())
                            <a href="{{ $sessions->nextPageUrl() }}"
                                class="px-3 py-1.5 text-sm bg-white hover:bg-gray-100 rounded-lg border">Next &rarr;</a>
                        @else
                            <span class="px-3 py-1.5 text-sm text-gray-400 rounded-lg border">Next &rarr;</span>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function refreshData() {
                location.reload();
            }

            // Auto-refresh every 30 seconds for active sessions
            setInterval(() => {
                const activeSessionCards = document.querySelectorAll('[class*="bg-blue-100"], [class*="bg-purple-100"]');
                if (activeSessionCards.length > 0) {
                    // Only refresh if there are active sessions
                    fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(response => {
                        if (response.ok) {
                            // Could update specific elements instead of full reload
                            console.log('Data refreshed');
                        }
                    });
                }
            }, 30000);
        </script>
    @endpush
@endsection