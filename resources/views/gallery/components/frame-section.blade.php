{{-- Modern Frame Section --}}
<section class="fade-in-delay mb-8 max-w-5xl mx-auto">
    <div class="glass-card">
        <div class="p-8">
            {{-- Section Header --}}
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div>
                        <h2 class="heading-2 mb-0">Premium Frame</h2>
                        <p class="text-muted">Frame hasil foto terbaik siap download</p>
                    </div>
                </div>
                <div class="badge badge-accent">
                    <i class="fas fa-sparkles mr-1"></i>
                    HD Quality
                </div>
            </div>
            
            {{-- Frame Display --}}
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                {{-- Frame Image --}}
                <div class="lg:col-span-1">
                    <div class="relative group frame-container">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-600 rounded-2xl blur opacity-25 group-hover:opacity-50 transition-opacity"></div>
                        <div class="relative bg-white p-3 rounded-2xl shadow-xl">
                            <img src="{{ $frame->preview_url ?? asset('images/frame-placeholder.png') }}" 
                                 alt="FOTOKU Premium Frame - {{ $photoSession->session_code }} - {{ $photoSession->customer_name }} Final Photo Collage"
                                 class="w-full h-auto rounded-xl transition-transform duration-300 group-hover:scale-105"
                                 title="Premium Photo Frame {{ $photoSession->session_code }}"
                                 loading="eager"
                                 onclick="openPhotoViewer('{{ $frame->preview_url ?? asset('images/frame-placeholder.png') }}', 0)">
                        </div>
                    </div>
                </div>
                
                {{-- Frame Info --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="text-center lg:text-left">
                        <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm font-medium mb-4">
                            <i class="fas fa-check-circle"></i>
                            <span>Siap Download</span>
                        </div>
                        <h3 class="text-xl lg:text-2xl font-bold text-gray-800 mb-3">
                            Frame Premium Anda
                        </h3>
                        <p class="text-gray-600 mb-2">Koleksi {{ $photos->count() }} foto terbaik dalam satu frame</p>
                        <p class="text-sm text-gray-500">Format JPG • Resolusi Tinggi • Siap Cetak 4x6 (2 strip 2x3)</p>
                    </div>
                    
                    {{-- Download Section --}}
                    <div class="space-y-4">
                        <a href="{{ $frame->download_url ?? '#' }}" 
                           class="btn btn-primary w-full justify-center text-base py-4 {{ !isset($frame->download_url) ? 'opacity-50 cursor-not-allowed' : '' }}"
                           {{ !isset($frame->download_url) ? 'disabled' : '' }}>
                            <i class="fas fa-download"></i>
                            Download Frame Premium
                        </a>
                        
                        {{-- Frame Stats --}}
                        <div class="grid grid-cols-3 gap-3 text-center text-sm">
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="font-semibold text-gray-800">300 DPI</div>
                                <div class="text-gray-500">Resolusi</div>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="font-semibold text-gray-800">4x6</div>
                                <div class="text-gray-500">Format (2 strip 2x3)</div>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <div class="font-semibold text-gray-800">JPG</div>
                                <div class="text-gray-500">File</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
