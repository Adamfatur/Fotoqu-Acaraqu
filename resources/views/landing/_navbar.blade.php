<nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/60 reveal">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a href="#beranda" class="flex items-center space-x-3">
        <img src="{{ asset('logo-fotoku-landscape.png') }}" alt="FotoQu" class="h-8 w-auto">
      </a>
      <div class="hidden md:flex items-center space-x-6">
        <a href="#beranda" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Beranda</a>
        <a href="#fitur" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Keunggulan</a>
        <a href="#cara-kerja" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Cara Kerja</a>
        <a href="#paket" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Paket</a>
        <a href="#faq" class="text-slate-600 hover:text-slate-900 text-sm font-medium">FAQ</a>
  <a href="#kontak" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-brand-orange">Chat WA</a>
      </div>
      
      <!-- Mobile menu button -->
      <div class="md:hidden">
        <button id="mobile-menu-button" class="text-slate-600 hover:text-slate-900 p-2">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden pb-4">
      <div class="flex flex-col space-y-3">
        <a href="#beranda" class="text-slate-600 hover:text-slate-900 text-sm font-medium px-4 py-2">Beranda</a>
        <a href="#fitur" class="text-slate-600 hover:text-slate-900 text-sm font-medium px-4 py-2">Keunggulan</a>
        <a href="#cara-kerja" class="text-slate-600 hover:text-slate-900 text-sm font-medium px-4 py-2">Cara Kerja</a>
        <a href="#paket" class="text-slate-600 hover:text-slate-900 text-sm font-medium px-4 py-2">Paket</a>
        <a href="#faq" class="text-slate-600 hover:text-slate-900 text-sm font-medium px-4 py-2">FAQ</a>
  <a href="#kontak" class="inline-flex items-center justify-center mx-4 py-2 rounded-lg text-sm font-semibold text-white bg-brand-orange">Chat WhatsApp</a>
      </div>
    </div>
  </div>
</nav>
