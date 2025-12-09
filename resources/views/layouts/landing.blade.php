<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'FotoQu | Photobooth Premium untuk Event Ramai & Memorable')</title>
  <meta name="description" content="@yield('meta_description', 'FotoQu — photobooth premium untuk wedding, corporate, ulang tahun, dan brand activation. Setup cepat, cetak instan 4x6 (2 strip 2x3), template custom, dan link download yang bisa dibagikan. Chat WhatsApp untuk cek tanggal & paket!')">
    <link rel="canonical" href="{{ url()->current() }}">
  <meta name="theme-color" content="#053a63">
  <link rel="icon" type="image/png" href="{{ asset('logo-fotoku-favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('logo-fotoku-favicon.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
  <meta property="og:title" content="@yield('og_title', 'FotoQu | Photobooth Premium untuk Event Memorable')">
  <meta property="og:description" content="@yield('og_description', 'Setup cepat, hasil tajam, cetak instan 4x6 (2 strip 2x3), dan link download. Cocok untuk wedding, corporate, ulang tahun, hingga brand activation.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('logo-fotoku-landscape.png') }}">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('tw_title', 'FotoQu | Photobooth Premium untuk Event Memorable')">
  <meta name="twitter:description" content="@yield('tw_description', 'Setup cepat, hasil tajam, cetak instan 4x6 (2 strip 2x3), dan link download. Cocok untuk berbagai jenis event.')">
    <meta name="twitter:image" content="{{ asset('logo-fotoku-landscape.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tailwind (CDN for this page) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    :root{
      --teal-blue:#053a63;/* Teal Blue */
      --carrot-orange:#f29223;/* Carrot Orange */
      --curious-blue:#1a90d6;/* Curious Blue */
      --picton-blue:#1fa8f0;/* Dodger Blue */
    }
  body{font-family:'Plus Jakarta Sans','Inter',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,'Noto Sans',sans-serif}
  h1,h2,h3,.heading{font-family:'Poppins','Plus Jakarta Sans','Inter',sans-serif}
  .brand-gradient{background:linear-gradient(135deg,var(--curious-blue) 0%,var(--teal-blue) 100%)}
  .brand-gradient-text{background:linear-gradient(135deg,var(--picton-blue) 0%,var(--teal-blue) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
  .text-brand-orange{color:var(--carrot-orange)}
  .bg-brand-orange{background-color:var(--carrot-orange)}
  .bg-brand-blue{background-color:var(--curious-blue)}
  .accent-orange-text{color:var(--carrot-orange)}
        .glass{background:rgba(255,255,255,.08);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.15)}
        .shadow-soft{box-shadow:0 10px 25px rgba(0,0,0,.08)}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        .float{animation:float 6s ease-in-out infinite}
  .chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;font-weight:600;font-size:.85rem;background:rgba(255,255,255,.75);color:#0f172a;border:1px solid rgba(15,23,42,.08)}
  .chip i{color:var(--carrot-orange)}
  /* gradient border wrapper */
  .g-border{position:relative;padding:1px;border-radius:16px;background:linear-gradient(135deg,var(--picton-blue),var(--carrot-orange))}
  .g-inner{background:#fff;border-radius:15px;height:100%}
  /* underline accent */
  .u-accent{background:linear-gradient(90deg,rgba(26,144,214,.2),rgba(241,150,40,.25));height:8px;border-radius:999px}
  /* Smooth reveal animations */
  .reveal{opacity:0;transform:translateY(32px);transition:opacity 1s cubic-bezier(.22,1,.36,1),transform 1s cubic-bezier(.22,1,.36,1)}
  .reveal.show{opacity:1;transform:translateY(0)}
  .reveal-right{opacity:0;transform:translateX(48px);transition:opacity 1.2s cubic-bezier(.22,1,.36,1),transform 1.2s cubic-bezier(.22,1,.36,1)}
  .reveal-right.show{opacity:1;transform:translateX(0)}
  .reveal-scale{opacity:0;transform:scale(.92) translateY(24px);transition:opacity 1.1s cubic-bezier(.22,1,.36,1),transform 1.1s cubic-bezier(.22,1,.36,1)}
  .reveal-scale.show{opacity:1;transform:scale(1) translateY(0)}
  
  /* Enhanced button animations */
  .btn-shine{position:relative;overflow:hidden;transition:all 0.3s cubic-bezier(.22,1,.36,1)}
  .btn-shine::after{content:"";position:absolute;top:0;left:-150%;width:140%;height:100%;background:linear-gradient(120deg,transparent 0,rgba(255,255,255,.5) 50%,transparent 100%);transform:skewX(-15deg);transition:left 0.8s cubic-bezier(.22,1,.36,1)}
  .btn-shine:hover{transform:translateY(-2px);box-shadow:0 20px 40px rgba(241,150,40,.3)}
  .btn-shine:hover::after{left:100%}
  
  /* Floating elements */
  @keyframes floatSlow{0%,100%{transform:translateY(0) rotate(0deg)}25%{transform:translateY(-12px) rotate(1deg)}75%{transform:translateY(8px) rotate(-1deg)}}
  @keyframes pulse-glow{0%,100%{box-shadow:0 0 20px rgba(31,166,238,.3)}50%{box-shadow:0 0 40px rgba(31,166,238,.5), 0 0 60px rgba(241,150,40,.2)}}
  
  .float-slow{animation:floatSlow 8s ease-in-out infinite}
  .pulse-glow{animation:pulse-glow 3s ease-in-out infinite}
  
  /* (removed) Hero text typewriter effect */
  
  /* Image hover effects */
  .image-hover{transition:all 0.6s cubic-bezier(.22,1,.36,1)}
  .image-hover:hover{transform:scale(1.08) rotate(2deg);box-shadow:0 25px 50px rgba(0,0,0,.2)}
  
  /* Gradient borders with animation */
  .g-border{position:relative;padding:2px;border-radius:20px;background:linear-gradient(135deg,var(--picton-blue),var(--carrot-orange),var(--curious-blue));background-size:200% 200%;animation:gradientShift 4s ease infinite}
  .g-inner{background:#fff;border-radius:18px;height:100%;transition:all 0.4s ease}
  .g-border:hover .g-inner{transform:translateY(-4px);box-shadow:0 15px 35px rgba(0,0,0,.1)}
  
  @keyframes gradientShift{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
  
  /* Staggered animations */
  .stagger-1{animation-delay:0.1s}
  .stagger-2{animation-delay:0.2s}
  .stagger-3{animation-delay:0.3s}
  .stagger-4{animation-delay:0.4s}
  .stagger-5{animation-delay:0.5s}
  .stagger-6{animation-delay:0.6s}
    </style>
    @stack('head')
</head>
<body class="bg-white text-slate-800">
  @yield('content')
  @stack('scripts')

    <!-- JSON-LD Schema -->
    <script type="application/ld+json">
      {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'FotoQu Photobooth',
        'url' => url('/'),
        'logo' => asset('logo-fotoku-landscape.png'),
        'image' => asset('logo-fotoku-landscape.png'),
  'telephone' => '+62 858-1918-4566',
        'sameAs' => [
          'https://instagram.com/fotoqu.photobooth',
        ],
      ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
    <script>
      // Enhanced Intersection Observer with more sophisticated animations
      const observer = new IntersectionObserver((entries)=>{
        entries.forEach(entry=>{
          if(entry.isIntersecting){
            entry.target.classList.add('show');
            observer.unobserve(entry.target);
          }
        })
      },{threshold:0.1, rootMargin:'0px 0px -60px 0px'});
      
      // Observe all animated elements
      document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.reveal, .reveal-right, .reveal-scale').forEach(el=>observer.observe(el));
        
        // Add mouse parallax effect to hero decorative elements  
        document.addEventListener('mousemove', (e) => {
          const { clientX, clientY } = e;
          const { innerWidth, innerHeight } = window;
          const xPos = (clientX / innerWidth) - 0.5;
          const yPos = (clientY / innerHeight) - 0.5;
          
          document.querySelectorAll('.float-slow').forEach((el, index) => {
            const multiplier = (index + 1) * 10;
            el.style.transform = `translate(${xPos * multiplier}px, ${yPos * multiplier}px)`;
          });
        });
        
        // Smooth scroll enhancement for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
          anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
              target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
              });
            }
          });
        });
        
        // Add entrance animations to staggered elements
        const staggerElements = document.querySelectorAll('[class*="stagger-"]');
        staggerElements.forEach((el, index) => {
          el.style.animationDelay = `${(index * 0.1) + 0.2}s`;
        });
        
        // Add shadow to navbar on scroll if it exists
        const nav = document.querySelector('nav');
        if(nav){
          const onScroll=()=>{
            if(window.scrollY>6){
              nav.classList.add('shadow-lg');
            } else {
              nav.classList.remove('shadow-lg');
            }
          };
          window.addEventListener('scroll', onScroll, {passive:true});
          onScroll();
        }

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if(mobileMenuButton && mobileMenu) {
          mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
          });
          
          // Close mobile menu when clicking on links
          mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
              mobileMenu.classList.add('hidden');
            });
          });
        }
      });
    </script>
</body>
</html>
