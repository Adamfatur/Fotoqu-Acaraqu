{{-- Photos Grid Component --}}
<style>
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    @media (max-width: 640px) {
        .photo-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }
    }

    .photo-item {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        border: 0.5px solid rgba(255, 255, 255, 0.3);
    }

    .photo-item:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 40px rgba(59, 130, 246, 0.2);
    }

    .photo-container {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: #f3f4f6;
    }

    .photo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .photo-item:hover .photo-container img {
        transform: scale(1.1);
    }

    .photo-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        display: flex;
        align-items: flex-end;
        padding: 20px;
    }

    .photo-item:hover .photo-overlay {
        opacity: 1;
    }

    .photo-info {
        padding: 20px;
        text-align: center;
    }

    .photo-number {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 12px;
    }
</style>

<div class="photo-grid">
    @foreach($photos as $photo)
    <div class="photo-item fade-in">
        {{-- Photo Container --}}
        <div class="photo-container">
            <img src="{{ $photo->thumbnail_url ?? $photo->preview_url }}" 
                 alt="Foto {{ $photo->sequence_number }}" 
                 loading="lazy">
            
            {{-- Hover Overlay --}}
            <div class="photo-overlay">
                <div class="text-white">
                    <div class="font-semibold text-lg">Foto #{{ $photo->sequence_number }}</div>
                    <div class="text-white/80 text-sm">Klik untuk mengunduh</div>
                </div>
            </div>
        </div>
        
        {{-- Photo Info --}}
        <div class="photo-info">
            <div class="photo-number">Foto #{{ $photo->sequence_number }}</div>
            <a href="{{ $photo->preview_url }}" 
               download="fotoku-{{ $photoSession->session_code }}-foto-{{ $photo->sequence_number }}.jpg"
               class="ios-btn ios-btn-secondary w-full">
                <i class="fas fa-download"></i>
                <span class="hidden sm:inline">Unduh Foto</span>
                <span class="sm:hidden">↓</span>
            </a>
        </div>
    </div>
    @endforeach
</div>
