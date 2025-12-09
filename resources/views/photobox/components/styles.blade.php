{{-- CSS Styles for Photobox Interface --}}
<style>
    /* Base styling - ensure proper background and text color */
    html, body {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #1e40af 50%, #1e3a8a 75%, #312e81 100%) !important;
        min-height: 100vh !important;
        color: white !important;
        font-family: 'Inter', sans-serif;
    }
    
    /* App container */
    #app {
        background: transparent !important;
        min-height: 100vh !important;
        color: white !important;
    }
    
    /* Default text colors */
    h1, h2, h3, h4, h5, h6, p, span, div, a {
        color: white !important;
    }
    
    /* Override any Tailwind white backgrounds that might interfere */
    .bg-white:not(.photo-item):not(.bg-white input):not(.bg-white select) {
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    
    .pulse-bg {
        animation: pulse-bg 2s ease-in-out infinite;
    }
    
    @keyframes pulse-bg {
        0%, 100% { background-color: rgba(255, 255, 255, 0.1); }
        50% { background-color: rgba(255, 255, 255, 0.2); }
    }
    
    .pulse-red {
        animation: pulse-red 1s ease-in-out infinite;
    }
    
    @keyframes pulse-red {
        0%, 100% { background-color: #dc2626; }
        50% { background-color: #ef4444; }
    }
    
    .countdown-circle {
        stroke-dasharray: 251.2;
        animation: countdown-fill 3s linear;
    }
    
    @keyframes countdown-fill {
        from {
            stroke-dashoffset: 251.2;
        }
        to {
            stroke-dashoffset: 0;
        }
    }

    /* === SIMPLE FULL SCREEN CAMERA CAPTURE MODE === */
    
    /* Hide header and main container padding during capture */
    .capture-fullscreen-mode header {
        display: none !important;
    }
    
    .capture-fullscreen-mode main {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .capture-fullscreen-mode #app {
        overflow: hidden !important;
    }
    
    /* Simple capture state for full screen mode */
    .capture-fullscreen-mode #capture-state {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 9999 !important;
        background: #000 !important;
    }
    
    /* Hardware-accelerated camera preview for smooth performance */
    #camera-preview {
        transform: translate3d(0, 0, 0);
        will-change: transform;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-perspective: 1000;
        perspective: 1000;
        -webkit-transform-style: preserve-3d;
        transform-style: preserve-3d;
    }
    
    /* Simple camera preview for full screen */
    .capture-fullscreen-mode #camera-preview {
        width: 100vw !important;
        height: 100vh !important;
        object-fit: cover !important;
        object-position: center !important;
        transform: translate3d(0, 0, 0) !important;
    }
    
    @keyframes countdown-fill {
        from { stroke-dashoffset: 251.2; }
        to { stroke-dashoffset: 0; }
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }
    
    .photo-item {
        aspect-ratio: 4/3;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .photo-item:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .photo-item.selected {
        border: 3px solid #10b981 !important;
        transform: scale(1.02);
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
    }
    
    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
        opacity: 0;
    }
    
    .photo-item img[src] {
        opacity: 1;
    }
    
    /* Touch-friendly buttons */
    .touch-btn {
        min-height: 48px;
        min-width: 120px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        user-select: none;
        touch-action: manipulation;
    }
    
    /* Fullscreen styles */
    .fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #1e40af 50%, #1e3a8a 75%, #312e81 100%);
    }
    
    /* Enhanced fullscreen mode for better tablet experience */
    .fullscreen-mode {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 10000 !important;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #1e40af 50%, #1e3a8a 75%, #312e81 100%) !important;
        overflow: hidden !important;
    }
    
    .fullscreen-mode #app {
        width: 100vw !important;
        height: 100vh !important;
        max-width: none !important;
        max-height: none !important;
    }
    
    /* Hide scrollbars in fullscreen */
    .fullscreen-mode::-webkit-scrollbar {
        display: none;
    }
    
    .fullscreen-mode {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    /* Touch optimizations for tablet */
    @media (max-width: 1024px) {
        .touch-btn {
            min-height: 56px;
            font-size: 18px;
            padding: 16px 32px;
        }
        
        .photo-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            padding: 24px;
        }
        
        .fullscreen-mode header {
            padding: 16px 24px;
        }
        
        .fullscreen-mode main {
            padding: 24px;
        }
    }
    
    /* iPad specific optimizations */
    @media (min-width: 768px) and (max-width: 1024px) {
        .fullscreen-mode .photo-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        
        .fullscreen-mode .touch-btn {
            min-height: 60px;
            font-size: 20px;
        }
        
        .fullscreen-mode h1, .fullscreen-mode h2 {
            font-size: 2.5rem;
        }
        
        .fullscreen-mode h3 {
            font-size: 1.75rem;
        }
    }
    
    /* Text visibility fixes - simplified */
    body * {
        color: white !important;
    }
    
    /* Exceptions for elements that should have different colors */
    .bg-white *, .bg-gray-50 *, .bg-gray-100 *, .bg-gray-200 * {
        color: #1f2937 !important;
    }
    
    /* Form elements */
    input, select, textarea {
        color: #1f2937 !important;
        background: white !important;
    }
    
    /* Preserve color classes for status indicators */
    [class*="text-green-"], [class*="text-blue-"], [class*="text-red-"], 
    [class*="text-yellow-"], [class*="text-amber-"], [class*="text-rose-"],
    [class*="text-teal-"], [class*="text-emerald-"], [class*="text-indigo-"] {
        color: inherit !important;
    }

    /* Frame selection styles */
    .frame-option .frame-preview-card {
        transition: all 0.3s ease;
    }
    
    .frame-option:hover .frame-preview-card {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }
    
    .frame-check {
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    /* Filter option styles */
    .filter-option {
        transition: all 0.3s ease;
    }
    
    .filter-option.active > div {
        border-color: #10b981 !important;
        background: rgba(16, 185, 129, 0.1) !important;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
    }
    
    .filter-option:hover > div {
        transform: translateX(4px);
        box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
    }
    
    /* Photo filter canvas */
    #photo-preview-canvas {
        max-width: 100%;
        max-height: 100%;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    /* Navigation button styles */
    #prev-photo-btn:disabled,
    #next-photo-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    
    #prev-photo-btn:disabled:hover,
    #next-photo-btn:disabled:hover {
        background: rgba(255, 255, 255, 0.2) !important;
    }
    
    /* Photo Filter State Specific Styles */
    #photo-filter-state {
        overflow: hidden !important;
    }
    
    #photo-filter-state canvas {
        max-width: 100% !important;
        max-height: calc(100vh - 250px) !important;
        object-fit: contain !important;
        display: block !important;
        margin: 0 auto !important;
    }
    
    /* Filter panel optimizations for MacBook Retina */
    #photo-filter-state .filter-option {
        min-height: 60px; /* Consistent height for all filter items */
    }
    
    #photo-filter-state .filter-option > div {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Optimized grid layout for filters */
    #photo-filter-state .grid-cols-2 {
        grid-template-rows: repeat(4, minmax(60px, auto));
        max-height: calc(100vh - 320px); /* Leave room for header and buttons */
    }
    
    /* Filter selection scrollbar styling */
    #photo-filter-state .overflow-y-auto::-webkit-scrollbar {
        width: 3px;
    }
    
    #photo-filter-state .overflow-y-auto::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
    }
    
    #photo-filter-state .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 2px;
    }
    
    #photo-filter-state .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    /* MacBook Retina specific optimizations */
    @media screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: 1280px) and (max-width: 1440px) {
        /* MacBook 13" and 14" Retina displays */
        #photo-filter-state .w-60 {
            width: 14rem !important; /* Slightly narrower for better fit */
        }
        
        #photo-filter-state .grid-cols-2 {
            gap: 0.25rem !important; /* Tighter spacing */
            max-height: calc(100vh - 300px) !important;
        }
        
        #photo-filter-state .filter-option {
            min-height: 55px !important; /* Slightly smaller on retina */
        }
        
        #photo-filter-state canvas {
            max-height: calc(100vh - 280px) !important;
        }
    }
    
    @media screen and (-webkit-min-device-pixel-ratio: 2) and (min-width: 1440px) and (max-width: 1680px) {
        /* MacBook 16" Retina display */
        #photo-filter-state .grid-cols-2 {
            max-height: calc(100vh - 280px) !important;
        }
    }
    
    /* Responsive adjustments for photo filter */
    @media (max-width: 1024px) {
        #photo-filter-state .w-60,
        #photo-filter-state .w-64 {
            width: 14rem !important;
        }
        
        #photo-filter-state canvas {
            max-height: calc(100vh - 300px) !important;
        }
        
        #photo-filter-state .grid-cols-2 {
            max-height: calc(100vh - 340px) !important;
        }
    }
    
    @media (max-width: 768px) {
        #photo-filter-state .flex {
            flex-direction: column !important;
        }
        
        #photo-filter-state .w-60,
        #photo-filter-state .w-64,
        #photo-filter-state .lg\\:w-72 {
            width: 100% !important;
            margin-top: 1rem !important;
        }
        
        #photo-filter-state canvas {
            max-height: 40vh !important;
        }
        
        #photo-filter-state .grid-cols-2 {
            grid-template-columns: repeat(4, 1fr) !important; /* 4 columns on mobile */
            max-height: 120px !important;
        }
        
        #photo-filter-state .filter-option {
            min-height: 50px !important;
        }
    }
    
    /* Better touch targets for mobile */
    @media (max-width: 768px) {
        #photo-filter-state .filter-option > div {
            padding: 0.5rem !important;
        }
        
        #photo-filter-state button {
            min-height: 44px !important;
            touch-action: manipulation;
        }
    }

    /* Pulse animation for important buttons */
    .pulse-animation {
        animation: pulse-glow 2s ease-in-out infinite;
    }
    
    @keyframes pulse-glow {
        0%, 100% { 
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            transform: scale(1);
        }
        50% { 
            box-shadow: 0 6px 25px rgba(16, 185, 129, 0.5);
            transform: scale(1.02);
        }
    }

    /* Enhanced Photo Filter State - Responsive Layout */
    #photo-filter-state {
        background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(59, 130, 246, 0.1) 50%, rgba(99, 102, 241, 0.1) 100%);
    }
    
    /* Custom scrollbar for filter panel */
    .scrollbar-thin {
        scrollbar-width: thin;
    }
    
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* Filter option hover effects */
    .filter-option .border-2.border-transparent {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .filter-option.active .border-2 {
        border-color: rgba(16, 185, 129, 0.8) !important;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }
    
    /* Responsive layout adjustments */
    @media screen and (max-width: 1536px) {
        #photo-filter-state .w-80 {
            width: 20rem; /* 320px */
        }
        
        #photo-filter-state .lg\:w-96 {
            width: 22rem; /* 352px */
        }
        
        #photo-filter-state .xl\:w-\[420px\] {
            width: 24rem; /* 384px */
        }
    }
    
    @media screen and (max-width: 1280px) {
        #photo-filter-state .max-w-7xl {
            max-width: 1200px;
        }
        
        #photo-filter-state canvas {
            max-height: calc(100vh - 320px) !important;
        }
    }
    
    @media screen and (max-width: 1024px) {
        #photo-filter-state .flex {
            flex-direction: column;
        }
        
        #photo-filter-state .w-80 {
            width: 100%;
            max-height: 400px;
        }
        
        #photo-filter-state canvas {
            max-height: calc(50vh - 100px) !important;
        }
    }
    
    /* MacBook Retina optimizations - Updated */
    @media screen and (min-width: 1440px) and (max-width: 1728px) and (min-height: 900px) and (max-height: 1117px) {
        #photo-filter-state canvas {
            max-height: calc(100vh - 300px) !important;
        }
        
        #photo-filter-state .max-w-7xl {
            max-width: 1400px;
        }
    }
    
    /* Large desktop optimizations */
    @media screen and (min-width: 1920px) {
        #photo-filter-state .max-w-7xl {
            max-width: 1600px;
        }
        
        #photo-filter-state canvas {
            max-height: calc(100vh - 280px) !important;
        }
        
        #photo-filter-state .w-80 {
            width: 28rem; /* 448px */
        }
        
        #photo-filter-state .lg\:w-96 {
            width: 30rem; /* 480px */
        }
        
        #photo-filter-state .xl\:w-\[420px\] {
            width: 32rem; /* 512px */
        }
    }

    /* Processing state improvements */
    #processing-state {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
        backdrop-filter: blur(20px);
    }
    
    /* Processing progress bar enhancements */
    #processing-progress {
        box-shadow: 
            0 0 20px rgba(168, 85, 247, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    /* Completed state improvements */
    #completed-state {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
        backdrop-filter: blur(20px);
    }
    
    /* Security: Disable text selection on sensitive elements */
    .photobox-interface, .photo-item, .camera-view {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-touch-callout: none;
        -webkit-tap-highlight-color: transparent;
    }
    
    /* Security: Disable image drag */
    .photo-item img, .camera-view img {
        -webkit-user-drag: none;
        -khtml-user-drag: none;
        -moz-user-drag: none;
        -o-user-drag: none;
        user-drag: none;
        pointer-events: none;
    }
    
    /* Security: Hide from print */
    @media print {
        .photobox-interface, .security-sensitive {
            display: none !important;
        }
    }
    
    /* Production-only styles */
    @if(config('app.env') === 'production')
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.01);
        pointer-events: none;
        z-index: 9999;
    }
    @endif
</style>
