{{-- Modern Gallery Header --}}
<header class="fade-in mb-8">
    <div class="glass-card">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                {{-- Logo and Brand --}}
                <div class="flex items-center gap-4">
                    <img src="{{ asset('logo-fotoku-kotak.png') }}" alt="FOTOKU Logo" class="w-12 h-12 rounded-xl">
                    <div>
                        <h1 class="heading-1 mb-0">Gallery Foto</h1>
                        <p class="text-muted">Sesi {{ $photoSession->session_code }}</p>
                    </div>
                </div>
                
                {{-- Quick Stats --}}
                <div class="flex gap-3">
                    <div class="badge badge-primary">
                        <i class="fas fa-images mr-1"></i>
                        {{ $photos->count() }} Foto
                    </div>
                    @if($frame)
                    <div class="badge badge-accent">
                        <i class="fas fa-crown mr-1"></i>
                        1 Frame
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Customer Info Card --}}
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                        {{ strtoupper(substr($photoSession->customer_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $photoSession->customer_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $photoSession->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Sesi ID</p>
                    <p class="font-semibold text-gray-800">{{ $photoSession->session_code }}</p>
                </div>
            </div>
        </div>
    </div>
</header>
