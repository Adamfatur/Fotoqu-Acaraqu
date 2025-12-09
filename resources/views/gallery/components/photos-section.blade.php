{{-- Modern Photos Section --}}
<section class="fade-in-delay mb-8 max-w-5xl mx-auto">
    <div class="glass-card">
        <div class="p-8">
            {{-- Section Header --}}
            <div class="section-header">
                <div class="section-title">
                    <div class="section-icon">
                        <i class="fas fa-images"></i>
                    </div>
                    <div>
                        <h2 class="heading-2 mb-0">Koleksi Foto</h2>
                        <p class="text-muted">Semua foto dari sesi photography Anda</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-outline">
                        {{ $photos->count() }} foto tersedia
                    </div>
                </div>
            </div>

            @if($photos->count() > 0)
                {{-- Photos Grid Container --}}
                <div class="photo-grid">
                    @foreach($photos as $photo)
                        <div class="photo-item" data-index="{{ $loop->index }}">
                            <div class="photo-container">
                                <img src="{{ route('photobox.gallery.serve-photo', ['session' => $photoSession->session_code, 'photo' => $photo->id]) }}"
                                    alt="Photo {{ $loop->iteration }} - FOTOKU Session {{ $photoSession->session_code }} - {{ $photoSession->customer_name }}"
                                    class="photo-img"
                                    title="Photo #{{ $photo->sequence_number }} from {{ $photoSession->session_code }}"
                                    loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}"
                                    onclick="openPhotoViewer('{{ route('photobox.gallery.serve-photo', ['session' => $photoSession->session_code, 'photo' => $photo->id]) }}', {{ $loop->index }})"
                                    style="opacity: 1">

                                {{-- Photo Overlay --}}
                                <div class="photo-overlay">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium">Foto #{{ $photo->sequence_number }}</p>
                                            <p class="text-sm opacity-75">{{ $photoSession->created_at->format('d M Y') }}</p>
                                        </div>
                                        <button
                                            class="w-8 h-8 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center transition-all"
                                            onclick="openPhotoViewer('{{ route('photobox.gallery.serve-photo', ['session' => $photoSession->session_code, 'photo' => $photo->id]) }}', {{ $loop->index }}); event.stopPropagation();">
                                            <i class="fas fa-expand text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Download All Section --}}
                <div class="mt-8 text-center">
                    <div
                        class="flex flex-col md:flex-row items-center justify-center gap-4 p-6 bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full flex-shrink-0">
                            <i class="fas fa-download text-blue-600"></i>
                        </div>
                        <div class="text-center md:text-left w-full md:w-auto">
                            <h3 class="font-semibold text-gray-800 mb-1">Download Semua Foto</h3>
                            <p class="text-sm text-gray-600">Dapatkan semua {{ $photos->count() }} foto dalam satu file ZIP
                            </p>
                        </div>
                        <a href="{{ route('gallery.download.all', $photoSession) }}"
                            class="btn btn-primary w-full md:w-auto justify-center ml-0 md:ml-4">
                            <i class="fas fa-download"></i>
                            Download ZIP
                        </a>
                    </div>
                </div>
            @else
                {{-- No Photos State --}}
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tidak Ada Foto</h3>
                    <p class="text-gray-600">Foto sedang dalam proses atau belum tersedia</p>
                </div>
            @endif
        </div>
    </div>
</section>