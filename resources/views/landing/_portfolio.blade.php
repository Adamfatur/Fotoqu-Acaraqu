<section id="portofolio" class="py-24 bg-white relative">
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
        <div class="text-center max-w-3xl mx-auto mb-16 reveal">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900">Intip Hasil Jepretan Kita Yuk!</h2>
            <p class="text-lg text-slate-600">Bukan cuma janji manis, ini bukti nyata kualitas FotoQu. Jernih, tajam, dan pastinya bikin happy!</p>
        </div>

        @if($images->isEmpty())
             <div class="text-center p-12 bg-slate-50 rounded-3xl border border-dashed border-slate-300">
                <i class="fas fa-images text-4xl text-slate-300 mb-4"></i>
                <p class="text-slate-500">Belum ada foto yang diupload nih.</p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 auto-rows-[200px]">
                @foreach($images->take(6) as $index => $file) 
                    @php 
                        $fname = $file->getFilename(); 
                        // Make the first item span 2x2 for variety
                        $classes = ($index === 0) ? 'col-span-2 row-span-2' : ''; 
                    @endphp
                    
                    <div class="{{ $classes }} group relative overflow-hidden rounded-3xl cursor-pointer reveal-scale stagger-{{ $index + 1 }}">
                        <img src="{{ asset('portofolio/' . $fname) }}" alt="Portfolio FotoQu {{ $index }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                           <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <p class="text-white font-bold text-sm bg-white/20 backdrop-blur-md px-3 py-1 rounded-full inline-block">
                                    <i class="fas fa-camera mr-1"></i> FotoQu Moment
                                </p>
                           </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if($images->count() > 6)
                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-500 italic">Dan masih banyak lagi...</p>
                </div>
            @endif
        @endif

        <div class="mt-16 text-center reveal">
            <a href="https://instagram.com/fotoqu.photobooth" target="_blank" class="inline-flex items-center gap-2 text-[#1a90d6] font-bold hover:text-[#157bb7] group transition-colors">
                <i class="fab fa-instagram text-xl"></i>
                Lihat Lebih Banyak di Instagram
                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>
