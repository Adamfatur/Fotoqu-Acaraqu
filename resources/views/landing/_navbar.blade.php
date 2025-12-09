<nav class="fixed w-full z-50 transition-all duration-300 top-0 left-0 bg-white/80 backdrop-blur-md border-b border-white/20 shadow-sm" id="navbar">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">
    <div class="flex items-center justify-between h-20">
      <!-- Logo -->
      <div class="flex-shrink-0 flex items-center gap-3">
        <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
          <img class="h-10 w-auto transition-transform duration-300 group-hover:scale-110" src="{{ asset('logo-fotoku-landscape.png') }}" alt="FotoQu Logo">
        </a>
      </div>
      
      <!-- Desktop Menu -->
      <div class="hidden md:block">
        <div class="ml-10 flex items-baseline space-x-8">
          <a href="#beranda" class="text-slate-600 hover:text-[#1a90d6] px-3 py-2 rounded-md text-sm font-medium transition-colors">Beranda</a>
          <a href="#keunggulan" class="text-slate-600 hover:text-[#1a90d6] px-3 py-2 rounded-md text-sm font-medium transition-colors">Keunggulan</a>
          <a href="#cara-kerja" class="text-slate-600 hover:text-[#1a90d6] px-3 py-2 rounded-md text-sm font-medium transition-colors">Cara Kerja</a>
          <a href="#portofolio" class="text-slate-600 hover:text-[#1a90d6] px-3 py-2 rounded-md text-sm font-medium transition-colors">Portofolio</a>
        </div>
      </div>
      
      <!-- CTA Button -->
      <div class="hidden md:block">
        <a href="https://wa.me/6285819184566?text=Halo%20FotoQu!%20Mau%20booking%20photobooth%20dong" target="_blank" 
           class="bg-[#1a90d6] text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-md hover:bg-[#157bb7] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
           Booking Now
        </a>
      </div>
      
      <!-- Mobile menu button -->
      <div class="-mr-2 flex md:hidden">
        <button type="button" id="mobile-menu-button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#1a90d6]" aria-controls="mobile-menu" aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <i class="fas fa-bars text-xl"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div class="hidden md:hidden bg-white border-t border-slate-100 shadow-xl" id="mobile-menu">
    <div class="px-4 pt-2 pb-6 space-y-2">
      <a href="#beranda" class="text-slate-600 hover:text-[#1a90d6] hover:bg-slate-50 block px-3 py-3 rounded-lg text-base font-medium transition-colors">Beranda</a>
      <a href="#keunggulan" class="text-slate-600 hover:text-[#1a90d6] hover:bg-slate-50 block px-3 py-3 rounded-lg text-base font-medium transition-colors">Keunggulan</a>
      <a href="#cara-kerja" class="text-slate-600 hover:text-[#1a90d6] hover:bg-slate-50 block px-3 py-3 rounded-lg text-base font-medium transition-colors">Cara Kerja</a>
      <a href="#portofolio" class="text-slate-600 hover:text-[#1a90d6] hover:bg-slate-50 block px-3 py-3 rounded-lg text-base font-medium transition-colors">Portofolio</a>
      
      <div class="pt-4 mt-2 border-t border-slate-100">
          <a href="https://wa.me/6285819184566?text=Halo%20FotoQu!%20Mau%20booking%20photobooth%20dong" 
             class="w-full text-center bg-[#1a90d6] text-white block px-3 py-3.5 rounded-xl text-base font-bold shadow-md active:scale-95 transition-transform">
             Booking Sekarang
          </a>
      </div>
    </div>
  </div>
</nav>