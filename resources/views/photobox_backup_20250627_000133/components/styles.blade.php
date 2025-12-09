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
        animation: countdown-fill 1s linear;
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
</style>
