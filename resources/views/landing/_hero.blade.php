<section id="beranda"
  class="relative min-h-screen pt-24 md:pt-28 lg:pt-32 pb-16 scroll-mt-24 md:scroll-mt-28 lg:scroll-mt-32 flex items-center brand-gradient overflow-hidden">
  <div class="absolute inset-0 bg-black/20"></div>
  <!-- Enhanced decorative blobs with animation -->
  <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full blur-3xl opacity-30 float-slow pulse-glow"
    style="background:radial-gradient(circle at 30% 30%, #1fa6ee, transparent 70%)"></div>
  <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full blur-3xl opacity-30 float-slow"
    style="background:radial-gradient(circle at 70% 70%, var(--carrot-orange), transparent 70%); animation-delay: -2s">
  </div>
  <div class="absolute top-1/2 left-1/2 w-64 h-64 rounded-full blur-3xl opacity-20 float-slow"
    style="background:radial-gradient(circle, #1a90d6, transparent 80%); animation-delay: -4s"></div>

  <div class="relative max-w-7xl mx-auto px-6 lg:px-8 text-white w-full">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div class="text-center lg:text-left reveal">
        <div class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-8 md:mb-10 heading">
          <h1 class="mt-0">
            Photobooth <span
              class="bg-gradient-to-r from-[var(--carrot-orange)] to-[#fcd34d] bg-clip-text text-transparent">Termurah</span>
            se-Tangerang, Kualitas Tetap <span class="accent-orange-text">Juara!</span>
          </h1>
        </div>

        <div class="u-accent w-24 mx-auto lg:mx-0 mb-10 md:mb-12"></div>

        <p class="text-xl md:text-2xl text-white/95 mb-14 max-w-2xl mx-auto lg:mx-0 leading-relaxed stagger-2">
          Bikin acaramu makin asik tanpa bikin kantong bolong. Cetak instan, hasil jernih, dan softcopy langsung masuk
          HP via QR Code. <span class="font-bold text-[var(--carrot-orange)]">Mulai 1 Jutaan aja!</span>
        </p>

        <div class="flex flex-col sm:flex-row gap-5 justify-center lg:justify-start stagger-3">
          <a href="https://wa.me/6285819184566?text=Halo%20FotoQu!%20Mau%20booking%20paket%20termurah%20dong!"
            target="_blank" rel="noopener"
            class="btn-shine inline-flex items-center justify-center px-8 py-5 rounded-2xl font-bold text-slate-900 shadow-soft text-lg transform hover:scale-105 transition-all duration-300 bg-brand-orange">
            <i class="fas fa-calendar-check mr-2"></i> Amankan Tanggal
          </a>
          <a href="https://wa.me/6285819184566?text=Halo%20FotoQu!%20Boleh%20tanya-tanya%20pricelist%20lengkapnya?"
            target="_blank" rel="noopener"
            class="inline-flex items-center justify-center px-8 py-5 rounded-2xl font-bold border-2 border-white/60 hover:bg-white/20 hover:border-white/80 hover:scale-105 transition-all duration-300 text-lg">
            <i class="fab fa-whatsapp mr-2"></i> Tanya Dulu Yuk
          </a>
        </div>
      </div>

      <div class="hidden lg:grid grid-cols-2 gap-6 items-end justify-items-center reveal-right">
        <div class="glass rounded-3xl overflow-hidden shadow-soft image-hover stagger-4"
          style="width: 320px; height: 420px;">
          <img src="{{ asset('portofolio/foto1.jpg') }}" alt="FotoQu Photobooth Termurah Tangerang"
            class="w-full h-full object-cover">
        </div>
        <div class="glass rounded-3xl overflow-hidden shadow-soft translate-y-8 image-hover stagger-5"
          style="width: 280px; height: 360px;">
          <img src="{{ asset('portofolio/foto2.jpg') }}" alt="Hasil Foto Photobooth Jernih"
            class="w-full h-full object-cover">
        </div>
      </div>
    </div>
  </div>
</section>