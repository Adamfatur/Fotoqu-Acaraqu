<div class="space-y-6">
    <!-- Session Header -->
    <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            {{ substr($session->customer_name, 0, 1) }}
        </div>
        <div class="flex-1">
            <h4 class="text-xl font-semibold text-gray-800">{{ $session->customer_name }}</h4>
            @if($session->customer_email)
                <p class="text-gray-600">{{ $session->customer_email }}</p>
            @else
                <p class="text-gray-500 italic">(Email tidak disediakan)</p>
            @endif
            <div class="flex items-center space-x-4 mt-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($session->session_status === 'completed') bg-green-100 text-green-800
                    @elseif($session->session_status === 'in_progress') bg-blue-100 text-blue-800
                    @elseif($session->session_status === 'approved') bg-purple-100 text-purple-800
                    @else bg-yellow-100 text-yellow-800 @endif">
                    {{ ucfirst(str_replace('_', ' ', $session->session_status)) }}
                </span>
                <span class="text-sm text-gray-500">{{ $session->created_at->format('d M Y, H:i') }}</span>
            </div>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($session->total_price, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500">{{ $session->frame_slots }} slot frame</p>
        </div>
    </div>

    <!-- Session Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Session Info -->
        <div class="space-y-4">
            <h5 class="font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Sesi</h5>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Kode Sesi:</span>
                    <span class="font-medium">{{ $session->session_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Photobox:</span>
                    <span class="font-medium">{{ $session->photobox->code ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Frame Layout:</span>
                    <span class="font-medium">{{ $session->frame_slots }} slot</span>
                </div>
                @if($session->package)
                <div class="flex justify-between">
                    <span class="text-gray-600">Paket:</span>
                    <span class="font-medium">{{ $session->package->name }} ({{ $session->package->frame_slots }} slot)</span>
                </div>
                @endif
                <div class="flex justify-between border-t pt-2">
                    <span class="text-gray-600 font-semibold">Total:</span>
                    <span class="font-bold text-lg">Rp {{ number_format($session->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="space-y-4">
            <h5 class="font-semibold text-gray-800 border-b border-gray-200 pb-2">Informasi Pembayaran</h5>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Status Pembayaran:</span>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        @if($session->payment_status === 'paid') bg-green-100 text-green-800
                        @elseif($session->payment_status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($session->payment_status) }}
                    </span>
                </div>
                @php $lastPayment = $session->paymentLogs->sortByDesc('created_at')->first(); @endphp
                @if($lastPayment)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Metode Pembayaran:</span>
                        <span class="font-medium">{{ ucfirst($lastPayment->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dibayar:</span>
                        <span class="font-medium">{{ $lastPayment->created_at->format('d M Y, H:i') }}</span>
                    </div>
                @endif
                @if($session->admin)
                <div class="flex justify-between">
                    <span class="text-gray-600">Diproses oleh:</span>
                    <span class="font-medium">{{ $session->admin->name }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Photos Progress -->
    @if($session->photos->isNotEmpty())
    <div class="space-y-4">
        <h5 class="font-semibold text-gray-800 border-b border-gray-200 pb-2">Foto yang Diambil</h5>
    @php $totalPhotos = config('fotoku.total_photos', 3); @endphp
        <div class="flex items-center space-x-4 mb-4">
            <div class="flex-1">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Progress Pengambilan Foto</span>
                    <span>{{ $session->photos->count() }}/{{ $totalPhotos }} foto</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-1000" 
                         style="width: {{ min(($session->photos->count() / max($totalPhotos,1)) * 100, 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Photos Grid -->
        <div class="grid grid-cols-5 gap-2">
            @foreach($session->photos->take($totalPhotos) as $photo)
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 
                {{ $photo->is_selected ? 'border-purple-500' : 'border-gray-200' }}">
                <img src="{{ $photo->preview_url }}" alt="Photo {{ $photo->sequence_number }}" class="w-full h-full object-cover">
                <div class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 absolute bottom-0 left-0 right-0">
                    #{{ $photo->sequence_number }} @if($photo->is_selected) ✓ @endif
                </div>
            </div>
            @endforeach
            
            <!-- Empty slots -->
            @for($i = $session->photos->count(); $i < $totalPhotos; $i++)
            <div class="aspect-square bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                <i class="fas fa-camera text-gray-400"></i>
            </div>
            @endfor
        </div>
    </div>
    @endif

    <!-- Frame Preview -->
    @if($session->frame)
    <div class="space-y-4">
        <h5 class="font-semibold text-gray-800 border-b border-gray-200 pb-2">Frame Hasil</h5>
        
        <div class="bg-gray-50 rounded-xl p-4">
            <img src="{{ $session->frame->preview_url }}" alt="Frame" class="w-full max-w-md mx-auto rounded-lg shadow-lg">
            
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Frame dibuat: {{ $session->frame->created_at->format('d M Y, H:i') }}</p>
                @if($session->frame->email_sent_at)
                <p class="text-sm text-green-600">✓ Email terkirim (#{{ $session->frame->email_count ?? 1 }}): {{ $session->frame->email_sent_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Logs -->
    @if($session->paymentLogs->isNotEmpty())
    <div class="space-y-4">
        <h5 class="font-semibold text-gray-800 border-b border-gray-200 pb-2">Riwayat Pembayaran</h5>
        
        <div class="space-y-2">
            @foreach($session->paymentLogs as $log)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium">{{ ucfirst($log->payment_method) }}</p>
                    <p class="text-sm text-gray-600">{{ $log->created_at->format('d M Y, H:i') }}</p>
                    @if($log->notes)
                    <p class="text-sm text-gray-500">{{ $log->notes }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="font-bold">Rp {{ number_format($log->amount, 0, ',', '.') }}</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                        @if($log->status === 'completed') bg-green-100 text-green-800
                        @elseif($log->status === 'pending') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($log->status) }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
        @if($session->session_status === 'pending')
        <button onclick="approveSession('{{ $session->id }}')" 
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
            <i class="fas fa-check mr-2"></i>
            Approve
        </button>
        @endif
        
        @if($session->session_status === 'completed' && $session->frame)
        <button onclick="resendEmail('{{ $session->id }}')" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
            <i class="fas fa-envelope mr-2"></i>
            Kirim Ulang Email
        </button>
        @endif
        
        <a href="{{ route('admin.sessions.show', $session) }}" 
           class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors">
            <i class="fas fa-external-link-alt mr-2"></i>
            Detail Lengkap
        </a>
    </div>
</div>

<script>
function approveSession(sessionId) {
    if (confirm('Yakin ingin approve sesi ini?')) {
        axios.post(`/admin/sessions/${sessionId}/approve`)
            .then(() => {
                location.reload();
            })
            .catch(error => {
                alert('Gagal approve sesi');
            });
    }
}

function resendEmail(sessionId) {
    if (confirm('Yakin ingin mengirim ulang email?')) {
        axios.post(`/admin/sessions/${sessionId}/resend-email`)
            .then(() => {
                alert('Email berhasil dikirim ulang');
            })
            .catch(error => {
                alert('Gagal mengirim email');
            });
    }
}
</script>
