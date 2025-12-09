{{-- Modern Gallery Header --}}
<header class="fade-in mb-8">
    <div class="glass-card">
        <div class="p-6">
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4 text-center md:text-left">
                {{-- Logo and Brand --}}
                <div class="flex flex-col md:flex-row items-center gap-4">
                    <img src="{{ asset('logo-fotoku-kotak.png') }}" alt="FOTOKU Logo"
                        class="w-16 h-16 md:w-12 md:h-12 rounded-xl shadow-md">
                    <div>
                        <h1 class="heading-1 mb-0">Gallery Foto</h1>
                        <p class="text-muted">Sesi {{ $photoSession->session_code }}</p>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="flex gap-3 justify-center">
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
            <div
                class="flex flex-col sm:flex-row items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-3 text-center sm:text-left w-full sm:w-auto">
                    <div
                        class="w-12 h-12 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shadow-inner mx-auto sm:mx-0 text-xl md:text-base">
                        {{ strtoupper(substr($photoSession->customer_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg md:text-base">{{ $photoSession->customer_name }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ $photoSession->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div
                    class="text-center sm:text-right w-full sm:w-auto border-t sm:border-0 border-blue-200/50 pt-3 sm:pt-0">
                    <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Kode Sesi</p>
                    <p
                        class="font-mono font-bold text-gray-800 text-lg tracking-wide bg-white/50 px-3 py-1 rounded-lg inline-block">
                        {{ $photoSession->session_code }}</p>
                </div>
            </div>
        </div>
    </div>
</header>