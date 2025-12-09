<section id="portfolio" class="py-20 bg-slate-50">
  @php
    $dir = public_path('portofolio');
    $images = collect([]);
    if (is_dir($dir)) {
      $files = \Illuminate\Support\Facades\File::files($dir);
      $images = collect($files)
        ->filter(function($f){
          $ext = strtolower($f->getExtension());
          return in_array($ext, ['jpg','jpeg','png','webp','gif']);
        })
        ->sortBy(fn($f)=>$f->getFilename())
        ->values();
    }
  @endphp
  <div class="max-w-7xl mx-auto px-6 lg:px-8">
    <div class="text-center mb-10 reveal">
      <h2 class="text-4xl md:text-5xl font-extrabold mb-3 brand-gradient-text heading">Portofolio FotoQu</h2>
      <p class="text-lg text-slate-600">Cuplikan hasil real di berbagai event—warna konsisten, angle rapi, dan vibe yang dapet.</p>
    </div>

    @if($images->isEmpty())
      <div class="reveal-scale g-border"><div class="g-inner p-10 text-center">
        <p class="text-slate-600">Belum ada foto di folder <span class="font-semibold">public/portofolio</span>.</p>
        <p class="text-slate-500 text-sm mt-2">Tambahkan gambar (.jpg, .jpeg, .png, .webp, .gif) untuk menampilkan slider otomatis.</p>
      </div></div>
    @else
      <div class="relative">
        <!-- Slider viewport -->
        <div id="portfolio-slider" class="reveal-scale flex overflow-x-auto snap-x snap-mandatory gap-5 pb-3 scrollbar-hide" style="scroll-behavior:smooth">
          @foreach ($images as $file)
            @php $fname = $file->getFilename(); @endphp
            <a href="{{ asset('portofolio/' . $fname) }}"
               class="group shrink-0 w-[86vw] sm:w-[520px] md:w-[640px] lg:w-[820px] aspect-[3/2] relative overflow-hidden rounded-2xl shadow-soft snap-center"
               data-lightbox="portfolio">
              <img src="{{ asset('portofolio/' . $fname) }}" alt="FotoQu Portofolio {{ $fname }}" 
                   class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105 group-hover:rotate-[0.5deg]" loading="lazy">
              <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity" style="background:linear-gradient(to bottom, rgba(31,166,238,.0), rgba(241,150,40,.25))"></div>
            </a>
          @endforeach
        </div>

        <!-- Controls -->
        <button type="button" aria-label="Sebelumnya" 
                class="hidden sm:flex absolute left-0 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white shadow-lg border border-slate-200 items-center justify-center hover:bg-slate-50"
                data-action="prev">
          <i class="fa-solid fa-chevron-left text-slate-700"></i>
        </button>
        <button type="button" aria-label="Berikutnya" 
                class="hidden sm:flex absolute right-0 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white shadow-lg border border-slate-200 items-center justify-center hover:bg-slate-50"
                data-action="next">
          <i class="fa-solid fa-chevron-right text-slate-700"></i>
        </button>
      </div>
    @endif
  </div>
</section>

@push('head')
<style>
  /* Hide default scrollbar for a cleaner slider look */
  .scrollbar-hide{ scrollbar-width: none; }
  .scrollbar-hide::-webkit-scrollbar{ display: none; }
}</style>
@endpush

@push('scripts')
<script>
  // Simple lightbox without external libs (kept from previous version)
  (function(){
    const overlay = document.createElement('div');
    overlay.style.cssText='position:fixed;inset:0;background:rgba(0,0,0,.85);display:none;align-items:center;justify-content:center;z-index:60;padding:24px';
    const img = document.createElement('img');
    img.style.cssText='max-width:92vw;max-height:90vh;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.4)';
    overlay.appendChild(img);
    document.body.appendChild(overlay);
    overlay.addEventListener('click',()=>overlay.style.display='none');

    function bindLightbox(){
      document.querySelectorAll('[data-lightbox="portfolio"]').forEach(a=>{
        a.addEventListener('click',e=>{
          e.preventDefault();
          img.src = a.getAttribute('href');
          overlay.style.display='flex';
        })
      });
    }
    bindLightbox();

    // Slider controls
    const slider = document.getElementById('portfolio-slider');
    if(slider){
      const by = () => Math.max(320, Math.floor(slider.clientWidth * 0.9));
      const prevBtn = slider.parentElement.querySelector('[data-action="prev"]');
      const nextBtn = slider.parentElement.querySelector('[data-action="next"]');
      if(prevBtn && nextBtn){
        prevBtn.addEventListener('click',()=> slider.scrollBy({left:-by(), behavior:'smooth'}));
        nextBtn.addEventListener('click',()=> slider.scrollBy({left: by(), behavior:'smooth'}));
        // Show controls only if overflow exists
        const toggleControls = ()=>{
          const overflow = slider.scrollWidth > slider.clientWidth + 8;
          prevBtn.style.display = nextBtn.style.display = overflow ? 'flex' : 'none';
        };
        window.addEventListener('resize', toggleControls, {passive:true});
        setTimeout(toggleControls, 0);
      }
    }
  })();
</script>
@endpush
