{{-- Bonus GIF Section (aligned with other sections) --}}
<section class="fade-in-delay mb-8 max-w-5xl mx-auto">
    <div class="glass-card">
        <div class="p-8">
            {{-- Section Header --}}
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-film"></i>
                    </div>
                    <div>
                        <h2 class="heading-2 mb-0">Bonus GIF</h2>
                        <p class="text-muted">Animasi dari 3 foto sesi Anda</p>
                    </div>
                </div>
                @if(isset($gif) && $gif && ($gif->status ?? null) === 'completed')
                    <div class="badge badge-primary">Siap diunduh</div>
                @else
                    <div class="badge badge-outline">Sedang diproses</div>
                @endif
            </div>

            @if(isset($gif) && $gif)
                {{-- GIF Display --}}
                <div class="flex flex-col items-center gap-5">
                    <div class="relative group frame-container">
                        <div class="relative bg-white p-3 rounded-2xl shadow-xl">
                            <img 
                                src="{{ route('photobox.gallery.serve-gif', ['session' => $photoSession->session_code, 'gif' => $gif->id]) }}" 
                                alt="Animated GIF - {{ $photoSession->session_code ?? 'FOTOKU' }}"
                                class="w-full h-auto rounded-xl"
                                loading="eager"
                            >
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 flex-wrap justify-center">
                        <a class="btn btn-primary" href="{{ route('photobox.gallery.serve-gif', ['session' => $photoSession->session_code, 'gif' => $gif->id, 'download' => 1]) }}">
                            <i class="fa-solid fa-download"></i>
                            Unduh GIF
                        </a>
                        <a class="btn btn-secondary" target="_blank" href="{{ route('photobox.gallery.serve-gif', ['session' => $photoSession->session_code, 'gif' => $gif->id]) }}">
                            <i class="fa-solid fa-up-right-from-square"></i>
                            Buka di Tab Baru
                        </a>
                    </div>

                    <p class="text-sm text-gray-500 text-center">GIF ini adalah bonus dari 3 foto sesi Anda. Nikmati dan bagikan! ✨</p>
                </div>
            @else
                {{-- Processing State --}}
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-spinner fa-spin text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Bonus GIF Sedang Diproses</h3>
                    <p class="text-gray-600">Silakan kembali beberapa menit lagi untuk melihat hasilnya.</p>
                </div>
            @endif
        </div>
    </div>
    
</section>
