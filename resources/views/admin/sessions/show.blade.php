@extends('admin.layout')

@section('header', 'Detail Sesi Foto')
@section('description', 'Detail lengkap sesi foto ' . $session->session_code)

@section('content')
<style>
    :root {
        --brand-teal: #053a63;
        /* Teal Blue */
        --brand-orange: #f29223;
        /* Carrot Orange */
        --brand-curious: #1a90d6;
        /* Curious Blue */
        --brand-dodger: #1fa8f0;
        /* Dodger Blue */
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        border-radius: .75rem;
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

    .btn-accent {
        background: var(--brand-orange);
        color: #fff;
    }

    .btn-accent:hover {
        filter: brightness(1.05);
        box-shadow: 0 8px 20px rgba(242, 146, 35, .2);
        transform: translateY(-1px);
    }

    .btn-muted {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-muted:hover {
        background: #e5e7eb;
    }

    /* Outline variant for subtle primary action */
    .btn-outline {
        background: #fff;
        color: var(--brand-curious);
        border: 2px solid var(--brand-curious);
    }

    .btn-outline:hover {
        background: var(--brand-curious);
        color: #fff;
        box-shadow: 0 8px 20px rgba(26, 144, 214, .2);
        transform: translateY(-1px);
    }

    /* Ghost/Link-like button for low emphasis actions */
    .btn-ghost {
        background: transparent;
        color: var(--brand-curious);
        border: 2px solid transparent;
    }

    .btn-ghost:hover {
        background: rgba(26, 144, 214, .08);
        color: var(--brand-curious);
        transform: translateY(-1px);
    }

    /* Small size utility */
    .btn-sm {
        padding: 8px 14px;
        font-size: .8125rem;
        border-radius: .75rem;
    }

    .input-palette {
        border: 1px solid #d1d5db;
        border-radius: .75rem;
        padding: .5rem .75rem;
        transition: box-shadow .2s, border-color .2s;
    }

    .input-palette:focus {
        outline: none;
        border-color: var(--brand-curious);
        box-shadow: 0 0 0 3px rgba(26, 144, 214, .2);
    }

    .header-gradient {
        background: linear-gradient(90deg, rgba(26, 144, 214, .08), rgba(31, 168, 240, .08));
    }

    .avatar-gradient {
        background: linear-gradient(135deg, var(--brand-curious), var(--brand-dodger));
        color: #fff;
    }

    .price {
        color: var(--brand-curious);
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: .25rem .75rem;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 600;
    }

    .badge-created,
    .badge-pending {
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

    .badge-completed,
    .badge-paid {
        background: rgba(5, 58, 99, .12);
        color: var(--brand-teal);
    }

    .badge-cancelled,
    .badge-failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .dot-dodger {
        background: var(--brand-dodger);
    }

    /* Quick Action Buttons */
    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        border-radius: 1rem;
        padding: 14px 18px;
        color: #fff;
        font-weight: 700;
        transition: transform .15s ease, box-shadow .25s ease, filter .2s ease;
    }

    .action-btn .icon {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, .2);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .action-btn .texts {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }

    .action-btn .title {
        font-size: 1rem;
        letter-spacing: .2px;
    }

    .action-btn .subtitle {
        font-size: .75rem;
        font-weight: 500;
        opacity: .9;
        margin-top: 2px;
        letter-spacing: .15px;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .08), 0 6px 16px rgba(26, 144, 214, .18);
    }

    .action-download {
        background: linear-gradient(90deg, var(--brand-curious), var(--brand-dodger));
    }

    .action-print {
        background: linear-gradient(90deg, var(--brand-teal), #042845);
    }

    .action-accent {
        background: linear-gradient(90deg, var(--brand-orange), #f0a24d);
    }

    .action-muted {
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
    }

    .action-muted .icon {
        background: #e5e7eb;
        color: #374151;
    }

    .action-disabled {
        background: #d1d5db;
        color: #4b5563;
        cursor: not-allowed;
    }

    .action-disabled .icon {
        background: #cfd5dd;
        color: #4b5563;
    }

    /* Simple lightbox */
    .lightbox-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .8);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .lightbox-backdrop.open {
        display: flex;
    }

    .lightbox-img {
        max-width: 90vw;
        max-height: 90vh;
        border-radius: 12px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, .4);
    }

    .zoomable {
        cursor: zoom-in;
    }
</style>
<div class="space-y-6">
    <!-- Back Button -->
    <!-- Back Button & Actions -->
    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('admin.sessions.index') }}"
            class="inline-flex items-center justify-center px-4 py-2.5 btn btn-muted text-sm w-full sm:w-auto">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            @if($session->session_status === 'pending')
                <form action="{{ route('admin.sessions.approve', $session) }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-4 py-2.5 btn btn-primary shadow-sm hover:shadow-md">
                        <i class="fas fa-check mr-2"></i>
                        Setujui Sesi
                    </button>
                </form>
            @endif

            @if(!in_array($session->session_status, ['completed', 'cancelled', 'failed']))
                <button type="button" onclick="showCancelModal()"
                    class="w-full sm:w-auto px-4 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-xl transition-colors font-medium text-sm border border-red-200">
                    <i class="fas fa-times mr-2"></i>
                    Batalkan
                </button>
                <form id="cancelForm" action="{{ route('admin.sessions.cancel', $session) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="reason" id="cancelReason" value="">
                </form>
            @endif
        </div>
    </div>

    <!-- Session Header -->
    <div class="header-gradient rounded-2xl p-4 lg:p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div
                    class="flex-shrink-0 w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold avatar-gradient shadow-md">
                    {{ substr($session->customer_name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl lg:text-2xl font-bold text-gray-800 truncate">{{ $session->customer_name }}</h1>
                    @if($session->customer_email)
                        <p class="text-gray-600 text-sm truncate">{{ $session->customer_email }}</p>
                    @else
                        <p class="text-gray-500 italic text-sm">(Email tidak disediakan)</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="badge
                            @if($session->session_status === 'completed') badge-completed
                            @elseif($session->session_status === 'in_progress') badge-inprogress
                            @elseif($session->session_status === 'approved') badge-approved
                            @elseif($session->session_status === 'pending') badge-pending
                            @else badge-cancelled @endif">
                            {{ ucfirst(str_replace('_', ' ', $session->session_status)) }}
                        </span>
                        <span
                            class="text-xs text-gray-500 border-l border-gray-300 pl-2 ml-1">{{ $session->created_at->format('d M Y, H:i') }}</span>

                        @isset($queuePosition)
                            @if($queuePosition === 0)
                                <span class="badge badge-inprogress whitespace-nowrap" title="Sesi ini sedang berjalan">
                                    <span class="dot-dodger w-2 h-2 rounded-full mr-2 inline-block"></span>
                                    Berjalan
                                </span>
                            @elseif($queuePosition !== null)
                                <span class="badge badge-approved whitespace-nowrap" title="Posisi antrian pada photobox ini">
                                    Antrian: {{ $queuePosition }} {{ $queueTotal ? "/ $queueTotal" : '' }}
                                </span>
                            @endif
                        @endisset
                    </div>
                </div>
            </div>

            <div
                class="flex flex-row lg:flex-col justify-between items-center lg:items-end border-t lg:border-t-0 pt-4 lg:pt-0 gap-1">
                <div class="text-left lg:text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Total Harga</p>
                    <p class="text-2xl lg:text-3xl font-bold price">Rp
                        {{ number_format($session->total_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-right">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                        {{ $session->frame_slots }} Slot Frame
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Session Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Session Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 card-hover transition-all">
                <h3
                    class="text-lg font-semibold text-gray-800 mb-4 lg:mb-6 border-b border-gray-200 pb-2 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Informasi Sesi
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Kode Sesi</span>
                            <span
                                class="font-medium text-gray-900 font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $session->session_code }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Photobox</span>
                            <span class="font-medium text-gray-900 text-right">{{ $session->photobox->code ?? 'N/A' }}
                                <br class="sm:hidden"> <span
                                    class="text-xs text-gray-500">{{ $session->photobox->name ?? '' }}</span></span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Frame Layout</span>
                            <span class="font-medium text-gray-900">{{ $session->frame_slots }} slot</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Quantity</span>
                            <span class="font-medium text-gray-900">{{ $session->quantity }} frame</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Total Harga</span>
                            <span class="font-bold price">Rp
                                {{ number_format($session->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2 rounded hover:bg-gray-50">
                            <span class="text-gray-600 text-sm">Status Bayar</span>
                            <span
                                class="badge {{ $session->payment_status === 'paid' ? 'badge-paid' : ($session->payment_status === 'pending' ? 'badge-pending' : 'badge-failed') }}">
                                {{ ucfirst($session->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @isset($queuePosition)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-stream mr-2" style="color: var(--brand-curious)"></i>
                        Status Antrian Photobox
                    </h3>
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <div class="text-sm text-gray-600">Photobox</div>
                            <div class="font-semibold">{{ $session->photobox->code ?? 'N/A' }} —
                                {{ $session->photobox->name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="text-right">
                            @if($queuePosition === 0)
                                <div class="badge badge-inprogress">Sedang Berjalan</div>
                            @elseif($queuePosition !== null)
                                <div class="badge badge-approved">Posisi Antrian: {{ $queuePosition }}
                                    {{ $queueTotal ? "dari $queueTotal" : '' }}
                                </div>
                            @endif
                            @if($runningSession && $runningSession->id !== $session->id)
                                <div class="text-xs text-gray-500 mt-2">Sedang berjalan: {{ $runningSession->session_code }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endisset

            @push('scripts')
                <script>
                    (function () {
                        const sessionId = {{ $session->id }};
                        const progressUrl = "{{ route('admin.sessions.gif-progress', ['session' => $session->id]) }}";
                        const statusEl = document.getElementById('gifStatus');
                        const barEl = document.getElementById('gifProgressBar');
                        const progTextEl = document.getElementById('gifProgressText');
                        const stepTextEl = document.getElementById('gifStepText');
                        const openBtn = document.getElementById('gifOpenBtn');
                        const dlBtn = document.getElementById('gifDownloadBtn');
                        const previewContainer = document.getElementById('gifPreviewContainer');

                        async function tick() {
                            try {
                                const res = await fetch(progressUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                if (!res.ok) return;
                                const data = await res.json();
                                if (!data.success) return;

                                if (!data.exists) {
                                    if (statusEl) statusEl.textContent = 'NONE';
                                    return;
                                }

                                if (statusEl) statusEl.textContent = (data.status || '').toUpperCase();

                                if (data.status === 'processing') {
                                    const p = Math.max(0, Math.min(100, parseInt(data.progress || 0)));
                                    if (barEl) barEl.style.width = p + '%';
                                    if (progTextEl) progTextEl.textContent = p + '%';
                                    if (stepTextEl) stepTextEl.textContent = (data.step || 'processing') + ' — ' + p + '%';
                                }

                                if (data.status === 'failed') {
                                    if (previewContainer) {
                                        previewContainer.innerHTML = '<p class="text-red-500 text-sm mt-2">Gagal membuat GIF: ' + (data.error || 'unknown error') + '</p>';
                                    }
                                    openBtn && openBtn.classList.add('opacity-50', 'pointer-events-none');
                                    dlBtn && dlBtn.classList.add('opacity-50', 'pointer-events-none');
                                }

                                if (data.status === 'completed') {
                                    if (previewContainer) {
                                        previewContainer.innerHTML = '<img id="gifPreview" src="' + data.gif_url + '" alt="GIF" class="max-w-xs rounded" />';
                                    }
                                    if (openBtn) { openBtn.href = data.gif_url; openBtn.classList.remove('opacity-50', 'pointer-events-none'); }
                                    if (dlBtn) { dlBtn.href = data.download_url; dlBtn.classList.remove('opacity-50', 'pointer-events-none'); }
                                }
                            } catch (e) { /* ignore */ }
                        }

                        // Poll every 2.5s
                        setInterval(tick, 2500);
                        // Initial tick after load
                        setTimeout(tick, 500);
                    })();
                </script>
            @endpush

            <!-- Payment Management -->
            @if($session->payment_status === 'pending')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-credit-card mr-2" style="color: var(--brand-curious)"></i>
                        Konfirmasi Pembayaran
                    </h3>

                    <form action="{{ route('admin.sessions.payment', $session) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Jumlah Pembayaran
                                </label>
                                <input type="number" name="amount" value="{{ $session->total_price }}" min="0" step="1000"
                                    class="w-full input-palette" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Metode Pembayaran
                                </label>
                                <select name="payment_method" class="w-full input-palette" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="cash">Tunai</option>
                                    <option value="qris">QRIS</option>
                                    <option value="edc">EDC/Kartu</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea name="notes" rows="3" placeholder="Catatan pembayaran..."
                                class="w-full input-palette"></textarea>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="submit" class="px-6 py-3 btn btn-primary rounded-lg">
                                <i class="fas fa-check mr-2"></i>
                                Konfirmasi Pembayaran
                            </button>

                            <div class="text-sm text-gray-500">
                                Status akan otomatis berubah ke "paid" setelah konfirmasi
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Session Approval -->
            @if($session->session_status === 'created' && $session->payment_status === 'paid')
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-check-circle mr-2" style="color: var(--brand-teal)"></i>
                        Persetujuan Sesi
                    </h3>

                    <div class="rounded-lg p-4 mb-4"
                        style="background: rgba(5,58,99,.06); border: 1px solid rgba(5,58,99,.2)">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle mr-2" style="color: var(--brand-teal)"></i>
                            <span class="text-sm" style="color: var(--brand-teal)">
                                Pembayaran telah dikonfirmasi. Sesi siap untuk disetujui dan dimulai.
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('admin.sessions.approve', $session) }}" method="POST">
                        @csrf
                        <button type="button" onclick="showApprovalModal()" class="px-6 py-3 btn btn-secondary rounded-lg">
                            <i class="fas fa-thumbs-up mr-2"></i>
                            Setujui Sesi & Aktifkan Photobox
                        </button>
                    </form>
                </div>
            @endif

            <!-- Photos Section -->
            @if($session->photos->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-camera mr-2" style="color: var(--brand-curious)"></i>
                        Foto Sesi ({{ $session->photos->count() }})
                    </h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 lg:gap-4">
                        @foreach($session->photos as $photo)
                            <div class="relative group aspect-square">
                                <div
                                    class="w-full h-full bg-gray-100 rounded-xl overflow-hidden shadow-sm border border-gray-200">
                                    <img src="{{ route('photobox.serve-photo', ['photo' => $photo->id]) }}"
                                        alt="Photo {{ $photo->sequence_number }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 cursor-zoom-in"
                                        onclick="openLightbox('{{ route('photobox.serve-photo', ['photo' => $photo->id]) }}')"
                                        loading="lazy">
                                </div>
                                <div
                                    class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                    #{{ $photo->sequence_number }}
                                </div>
                                @if($photo->is_selected)
                                    <div
                                        class="absolute top-2 right-2 bg-teal-500 text-white w-6 h-6 flex items-center justify-center rounded-full shadow-md">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($session->selectedPhotos->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-800 mb-3">Foto Terpilih untuk Frame
                                ({{ $session->selectedPhotos->count() }})</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($session->selectedPhotos as $photo)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                        <i class="fas fa-image mr-1.5 opacity-70"></i>
                                        #{{ $photo->sequence_number }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Frame Section -->
            @if($session->frame)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-picture-o mr-2" style="color: var(--brand-teal)"></i>
                        Frame Final
                    </h3>

                    <div class="text-center">
                        @if($session->frame->s3_path)
                            <img src="{{ route('photobox.serve-frame', ['frame' => $session->frame->id]) }}" alt="Final Frame"
                                class="max-w-[420px] w-full h-auto rounded-xl shadow-lg mx-auto zoomable"
                                onclick="openLightbox('{{ route('photobox.serve-frame', ['frame' => $session->frame->id]) }}')"
                                onerror="this.parentElement.innerHTML='<div class=&quot;bg-gray-100 rounded-xl p-8&quot;><i class=&quot;fas fa-image text-gray-400 text-4xl mb-3&quot;></i><p class=&quot;text-gray-500&quot;>Frame tidak dapat dimuat</p></div>'">
                        @else
                            <div class="bg-gray-100 rounded-xl p-8">
                                <i class="fas fa-image text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">Frame sedang diproses...</p>
                            </div>
                        @endif

                        <div class="mt-4 flex justify-center space-x-3">
                            @if($session->frame->s3_path)
                                <a href="{{ route('photobox.serve-frame', ['frame' => $session->frame->id]) }}" target="_blank"
                                    class="px-4 py-2 btn btn-secondary rounded-xl">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    Lihat Full Size
                                </a>
                                <a href="{{ route('photobox.serve-frame', ['frame' => $session->frame->id]) }}"
                                    download="{{ $session->frame->filename }}" class="px-4 py-2 btn btn-primary rounded-xl">
                                    <i class="fas fa-download mr-2"></i>
                                    Download Frame
                                </a>
                            @endif

                            @if($session->frame->email_sent_at)
                                <span class="px-4 py-2 rounded-xl text-sm"
                                    style="background: rgba(5,58,99,.12); color: var(--brand-teal)">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    FOTOKU #{{ $session->frame->email_count }} terkirim:
                                    {{ $session->frame->email_sent_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Bonus GIF moved below Final Frame --}}
            @php($gif = $session->sessionGif)
            @php($statusText = $gif ? strtoupper($gif->status) : 'NONE')
            @php(
                $badgeClass = match ($statusText) {
                    'COMPLETED' => 'bg-green-100 text-green-700 border-green-200',
                    'FAILED' => 'bg-red-100 text-red-700 border-red-200',
                    'PROCESSING' => 'bg-blue-100 text-blue-700 border-blue-200',
                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                }
            )
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-wrap items-center justify-between mb-4 gap-3 border-b border-gray-200 pb-2">
                    <h3 class="text-lg font-semibold flex items-center gap-2 text-gray-800">
                        <i class="fas fa-gift" style="color: var(--brand-orange)"></i>
                        Bonus GIF
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                            Status: <span id="gifStatus">{{ $statusText }}</span>
                        </span>
                        @php($isCompleted = $gif && $gif->status === 'completed')
                        <a id="gifOpenBtn"
                            href="{{ $isCompleted ? route('public.serve-gif', ['gif' => $gif->id]) : '#' }}"
                            target="_blank"
                            class="btn btn-ghost btn-sm {{ $isCompleted ? '' : 'opacity-50 pointer-events-none' }}">
                            <i class="fas fa-up-right-from-square mr-2"></i>
                            Buka
                        </a>
                        <a id="gifDownloadBtn"
                            href="{{ $isCompleted ? route('public.serve-gif', ['gif' => $gif->id, 'download' => 1]) : '#' }}"
                            class="btn btn-primary btn-sm {{ $isCompleted ? '' : 'opacity-50 pointer-events-none' }}">
                            <i class="fas fa-download mr-2"></i>
                            Unduh
                        </a>
                    </div>
                </div>

                <div id="gifPreviewContainer" class="mt-4">
                    @if($gif && $gif->status === 'completed')
                    @php($sizeText = $gif->file_size ? number_format($gif->file_size / (1024 * 1024), 2) . ' MB' : null)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                            <div class="flex justify-center">
                                <img id="gifPreview" src="{{ route('public.serve-gif', ['gif' => $gif->id]) }}"
                                    alt="Bonus GIF" class="w-full max-w-[420px] rounded-xl border border-gray-200 shadow" />
                            </div>
                            <div class="space-y-3">
                                @if($sizeText)
                                    <p class="text-sm text-gray-600">Ukuran: <span
                                            class="font-medium text-gray-800">{{ $sizeText }}</span></p>
                                @endif
                                {{-- Actions are available in the header --}}
                            </div>
                        </div>
                    @elseif($gif && $gif->status === 'failed')
                        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-sm" id="gifError">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Gagal membuat GIF: {{ $gif->error_message }}
                        </div>
                    @else
                    <div class="space-y-2">
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div id="gifProgressBar" class="h-3 rounded-full transition-all duration-300"
                                style="width: {{ $gif && $gif->progress ? $gif->progress : 0 }}%; background: linear-gradient(90deg, var(--brand-curious), var(--brand-dodger));">
                            </div>
                        </div>
                        <p id="gifStepText" class="text-gray-600 text-sm">
                            <i class="fas fa-spinner fa-spin mr-2" style="color: var(--brand-curious)"></i>
                            <span>{{ $gif ? ($gif->step ?? 'starting') : 'none' }}</span>
                            — <span id="gifProgressText">{{ $gif && $gif->progress ? $gif->progress : 0 }}%</span>
                        </p>
                        {{-- Actions remain in the header; disabled until completed --}}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Activity & Payment -->
        <div class="space-y-6">

            <!-- Customer Gallery Link -->
            @php($galleryUrl = route('photobox.user-gallery', ['session' => $session->session_code]))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-link mr-2" style="color: var(--brand-curious)"></i>
                    Link Galeri Pelanggan
                </h3>
                <p class="text-sm text-gray-600 mb-3">Bagikan link ini ke pelanggan untuk melihat dan mengunduh foto.
                </p>
                <div class="flex flex-col gap-2">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 font-mono text-sm truncate"
                        id="galleryLinkValue">{{ $galleryUrl }}</div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="copyGalleryLink()"
                            class="px-3 py-2 btn btn-secondary rounded-lg">
                            <i class="fas fa-copy mr-2"></i> Salin
                        </button>
                        <a href="{{ $galleryUrl }}" target="_blank" rel="noopener"
                            class="px-3 py-2 btn btn-primary rounded-lg">
                            <i class="fas fa-external-link-alt mr-2"></i> Buka
                        </a>
                        <button type="button" onclick="openGalleryQrModal()"
                            class="px-3 py-2 btn btn-outline rounded-lg">
                            <i class="fas fa-qrcode mr-2"></i> QR Code
                        </button>
                    </div>
                </div>
            </div>

            <!-- Payment Logs -->
            @if($session->paymentLogs->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-money-bill-wave mr-2" style="color: var(--brand-curious)"></i>
                        Riwayat Pembayaran
                    </h3>

                    <div class="space-y-4">
                        @foreach($session->paymentLogs as $payment)
                            <div class="border border-gray-200 rounded-xl p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-medium text-gray-800">Rp
                                        {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-500">{{ $payment->created_at->format('d/m H:i') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">{{ ucfirst($payment->payment_method) }}</span>
                                    <span class="text-gray-600">{{ $payment->admin->name ?? 'System' }}</span>
                                </div>
                                @if($payment->notes)
                                    <p class="text-sm text-gray-500 mt-2">{{ $payment->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Activity Logs -->
            @if($session->activityLogs->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                        <i class="fas fa-history mr-2" style="color: var(--brand-curious)"></i>
                        Aktivitas Terbaru
                    </h3>

                    <div class="space-y-3">
                        @foreach($session->activityLogs->take(10) as $log)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2 dot-dodger"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-800">{{ $log->description }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs text-gray-500">{{ $log->created_at->format('d/m H:i') }}</span>
                                        @if($log->user)
                                            <span class="text-xs text-gray-500">• {{ $log->user->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b border-gray-200 pb-2">
                    <i class="fas fa-cogs mr-2" style="color: var(--brand-teal)"></i>
                    Aksi Cepat
                </h3>

                <div class="space-y-3">
                    @if($session->session_status === 'completed' && $session->frame)
                        <!-- Download Frame -->
                        <a href="{{ route('photobox.download-frame', ['frame' => $session->frame->id]) }}"
                            download="FOTOKU_Frame_{{ $session->session_code }}.jpg" class="action-btn action-download">
                            <span class="icon"><i class="fas fa-download"></i></span>
                            <span class="texts">
                                <span class="title">Download Frame</span>
                                <span class="subtitle">Download file berkualitas tinggi</span>
                            </span>
                        </a>

                        <!-- Print Frame -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <form action="{{ route('admin.sessions.print', $session) }}" method="GET" target="_blank">
                                <input type="hidden" name="paper" value="100x148mm">
                                <button type="submit" class="action-btn action-print">
                                    <span class="icon"><i class="fas fa-print"></i></span>
                                    <span class="texts">
                                        <span class="title">Print (Dialog)</span>
                                        <span class="subtitle">100×148 mm • Borderless</span>
                                    </span>
                                </button>
                            </form>
                            <form action="{{ route('admin.sessions.print', $session) }}" method="GET" target="_blank">
                                <input type="hidden" name="paper" value="100x148mm">
                                <input type="hidden" name="autoprint" value="1">
                                <button type="submit" class="action-btn"
                                    style="background: linear-gradient(90deg,#10b981,#059669); color:#fff">
                                    <span class="icon"><i class="fas fa-bolt"></i></span>
                                    <span class="texts">
                                        <span class="title">Print Langsung</span>
                                        <span class="subtitle">Tanpa dialog (Chrome kiosk)</span>
                                    </span>
                                </button>
                            </form>
                        </div>

                        @if($session->customer_email)
                            <form action="{{ route('admin.sessions.resend-email', $session) }}" method="POST">
                                @csrf
                                <button type="submit" class="action-btn action-accent">
                                    <span class="icon"><i class="fas fa-envelope"></i></span>
                                    <span class="texts">
                                        <span class="title">Kirim Ulang Email</span>
                                    </span>
                                </button>
                            </form>
                        @else
                            <div class="action-btn action-disabled">
                                <span class="icon"><i class="fas fa-envelope"></i></span>
                                <span class="texts">
                                    <span class="title">Email Tidak Tersedia</span>
                                    <span class="subtitle">Customer tidak memberikan email</span>
                                </span>
                            </div>
                        @endif
                    @endif

                    @if($session->session_status === 'in_progress' && $session->photos->count() >= $session->frame_slots)
                        <form action="{{ route('admin.sessions.create-frame', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-btn action-accent">
                                <span class="icon"><i class="fas fa-magic"></i></span>
                                <span class="texts">
                                    <span class="title">Generate Frame</span>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if($session->session_status === 'approved')
                        <form action="{{ route('admin.sessions.simulate', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="action-btn action-download">
                                <span class="icon"><i class="fas fa-play"></i></span>
                                <span class="texts">
                                    <span class="title">Simulasi Foto</span>
                                </span>
                            </button>
                        </form>
                    @endif

                    <button onclick="window.location.reload()" class="action-btn action-muted">
                        <span class="icon"><i class="fas fa-sync-alt"></i></span>
                        <span class="texts">
                            <span class="title">Refresh Data</span>
                        </span>
                    </button>

                    @if(in_array($session->session_status, ['cancelled', 'failed', 'in_progress', 'photo_selection', 'completed']) && $session->photos->count() > 0)
                        <!-- Retry Processing: For sessions with photos but failed processing -->
                        <!-- Retry Processing: For sessions with photos but failed processing -->
                        <form action="{{ route('admin.sessions.retry-processing', $session) }}" method="POST"
                            onsubmit="const btn = this.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class=\'fas fa-spinner fa-spin mr-2\'></i>Memproses...';">
                            @csrf
                            <button type="submit" class="action-btn"
                                style="background: linear-gradient(90deg,#8b5cf6,#7c3aed); color:#fff">
                                <span class="icon"><i class="fas fa-redo-alt"></i></span>
                                <span class="texts">
                                    <span class="title">Proses Ulang (Rescue)</span>
                                    <span class="subtitle">Buat Frame & GIF dari foto yang ada</span>
                                </span>
                            </button>
                        </form>
                    @endif

                    @if(in_array($session->session_status, ['cancelled', 'failed']))
                        <!-- Danger Zone: Purge assets for this session only -->
                        <form action="{{ route('admin.sessions.purge-assets', $session) }}" method="POST"
                            onsubmit="return handlePurgeConfirm(event)">
                            @csrf
                            <button type="submit" class="action-btn"
                                style="background: linear-gradient(90deg,#ef4444,#dc2626); color:#fff">
                                <span class="icon"><i class="fas fa-trash-alt"></i></span>
                                <span class="texts">
                                    <span class="title">Hapus Foto & Frame (Sesi Ini)</span>
                                    <span class="subtitle">Hanya menghapus foto & frame milik sesi ini dari storage &
                                        database</span>
                                </span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-all duration-300 scale-95">
        <div class="text-center">
            <!-- Icon -->
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6"
                style="background: linear-gradient(135deg, var(--brand-curious), var(--brand-dodger))">
                <i class="fas fa-check-circle text-white text-3xl"></i>
            </div>

            <!-- Title -->
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Setujui Sesi Foto?</h3>

            <!-- Description -->
            <div class="text-gray-600 mb-6 space-y-2">
                <p>Anda akan menyetujui sesi untuk:</p>
                <div class="rounded-lg p-4 text-left" style="background: rgba(26,144,214,.08)">
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-user" style="color: var(--brand-curious)"></i>
                        <span class="font-semibold">{{ $session->user_name }}</span>
                    </div>
                    <div class="flex items-center space-x-2 mb-2">
                        <i class="fas fa-camera" style="color: var(--brand-teal)"></i>
                        <span>{{ $session->photobox->code ?? 'N/A' }} - {{ $session->frame_slots }} slot</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-money-bill-wave" style="color: var(--brand-curious)"></i>
                        <span>Rp {{ number_format($session->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    Photobox akan aktif dan siap digunakan setelah disetujui
                </p>
            </div>

            <!-- Actions -->
            <div class="flex space-x-3">
                <button onclick="hideApprovalModal()"
                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </button>
                <button onclick="confirmApproval()" class="flex-1 px-6 py-3 btn btn-secondary rounded-lg">
                    <i class="fas fa-check mr-2"></i>
                    Setujui
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Simple lightbox implementation
        const lb = document.createElement('div');
        lb.className = 'lightbox-backdrop';
        lb.innerHTML = '<img class="lightbox-img" alt="preview" />';
        document.body.appendChild(lb);
        lb.addEventListener('click', () => lb.classList.remove('open'));
        function openLightbox(src) {
            const img = lb.querySelector('img');
            img.src = src;
            lb.classList.add('open');
        }
        function showApprovalModal() {
            const modal = document.getElementById('approvalModal');
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function hideApprovalModal() {
            const modal = document.getElementById('approvalModal');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        function confirmApproval() {
            // Submit the form
            document.querySelector('form[action*="approve"]').submit();
        }

        // Close modal when clicking outside
        document.getElementById('approvalModal').addEventListener('click', function (e) {
            if (e.target === this) {
                hideApprovalModal();
            }
        });

        // Cancel session functions
        function showCancelModal() {
            const modal = document.createElement('div');
            modal.id = 'cancelModal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';

            modal.innerHTML = `
                            <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-all duration-300 scale-95">
                                <div class="text-center">
                                    <!-- Icon -->
                                    <div class="w-20 h-20 bg-gradient-to-br from-red-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <i class="fas fa-times-circle text-white text-3xl"></i>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Batalkan Sesi Foto?</h3>

                                    <!-- Description -->
                                    <div class="text-gray-600 mb-6 space-y-2">
                                        <p>Apakah Anda yakin ingin membatalkan sesi untuk:</p>
                                        <div class="bg-red-50 rounded-lg p-4 text-left">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <i class="fas fa-user text-red-500"></i>
                                                <span class="font-semibold">{{ $session->customer_name }}</span>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-camera text-red-500"></i>
                                                <span>{{ $session->photobox->code ?? 'N/A' }} - {{ $session->frame_slots }} slot</span>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 text-left mb-2">
                                                Alasan Pembatalan (Opsional)
                                            </label>
                                            <textarea id="reasonText" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                                                placeholder="Masukkan alasan pembatalan..."></textarea>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex space-x-3">
                                        <button onclick="hideCancelModal()" 
                                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-arrow-left mr-2"></i>
                                            Kembali
                                        </button>
                                        <button onclick="confirmCancel()" 
                                                class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white rounded-lg hover:from-red-600 hover:to-pink-600 transition-all">
                                            <i class="fas fa-check mr-2"></i>
                                            Batalkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;

            document.body.appendChild(modal);
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);

            // Close modal when clicking outside
            modal.addEventListener('click', function (e) {
                if (e.target === this) {
                    hideCancelModal();
                }
            });
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            if (modal) {
                modal.querySelector('.transform').classList.remove('scale-100');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.remove();
                }, 300);
            }
        }

        function confirmCancel() {
            const reason = document.getElementById('reasonText').value;
            document.getElementById('cancelReason').value = reason;
            document.getElementById('cancelForm').submit();
        }

        // Copy gallery link helper
        async function copyGalleryLink() {
            const el = document.getElementById('galleryLinkValue');
            const text = el?.textContent?.trim();
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
                window.showToast && window.showToast({ title: 'Tersalin', message: 'Link galeri disalin ke clipboard', type: 'success' });
            } catch (e) {
                console.error(e);
                window.showToast && window.showToast({ title: 'Gagal', message: 'Gagal menyalin link', type: 'error' });
            }
        }

        // Purge confirm using modal
        async function handlePurgeConfirm(e) {
            e.preventDefault();
            const ok = await (window.showConfirmModal ? window.showConfirmModal({ title: 'Hapus Foto & Frame (Sesi Ini)?', message: 'Aksi ini hanya menghapus foto dan frame milik sesi ini dari penyimpanan lokal, S3, dan database. Tindakan ini tidak dapat dibatalkan.', confirmText: 'Hapus', theme: 'danger' }) : Promise.resolve(confirm('Yakin hapus hanya untuk sesi ini?')));
            if (ok) {
                e.target.submit();
            }
            return false;
        }

        // Simple QR modal using QRCode.js CDN (loaded on demand)
        let qrModalEl = null;
        async function openGalleryQrModal() {
            const url = document.getElementById('galleryLinkValue')?.textContent?.trim();
            if (!url) return;

            if (!qrModalEl) {
                qrModalEl = document.createElement('div');
                qrModalEl.id = 'galleryQrModal';
                qrModalEl.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
                qrModalEl.innerHTML = `
                                <div class="bg-white rounded-2xl p-6 w-full max-w-sm mx-4 shadow-xl">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800">QR Galeri Pelanggan</h3>
                                        <button onclick="closeGalleryQrModal()" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                                    </div>
                                    <div id="qrContainer" class="flex items-center justify-center p-4">
                                        <div id="qrCanvas"></div>
                                    </div>
                                    <div class="text-center text-xs text-gray-500 mt-2 break-all">${url}</div>
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button onclick="closeGalleryQrModal()" class="px-3 py-2 btn btn-muted">Tutup</button>
                                    </div>
                                </div>`;
                document.body.appendChild(qrModalEl);
                // Close on backdrop click
                qrModalEl.addEventListener('click', (e) => { if (e.target === qrModalEl) closeGalleryQrModal(); });
            }

            // Ensure QR lib is available
            if (!window.QRCode) {
                await loadQrLib();
            }

            // Render QR
            const qrTarget = qrModalEl.querySelector('#qrCanvas');
            qrTarget.innerHTML = '';
            new QRCode(qrTarget, {
                text: url,
                width: 240,
                height: 240,
                correctLevel: QRCode.CorrectLevel.M,
                colorDark: '#000000',
                colorLight: '#ffffff',
            });

            // Update URL text in modal (in case changed)
            const urlText = qrModalEl.querySelector('.text-center.text-xs');
            if (urlText) urlText.textContent = url;

            qrModalEl.classList.remove('hidden');
        }

        function closeGalleryQrModal() {
            if (qrModalEl) qrModalEl.classList.add('hidden');
        }

        function loadQrLib() {
            return new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
                s.onload = () => resolve();
                s.onerror = () => reject(new Error('Failed to load qrcode.js'));
                document.head.appendChild(s);
            });
        }
    </script>
@endpush
@endsection