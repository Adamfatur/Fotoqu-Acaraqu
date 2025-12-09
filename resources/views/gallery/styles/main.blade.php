<style>
    /* Import Google Fonts */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap');

    :root {
        /* Brand Colors */
        --teal-blue: #053b62;
        --sandy-brown: #f1a54e;
        --curious-blue: #1a8fd6;
        --picton-blue: #38a4e2;
        
        /* Neutral Colors */
        --white: #ffffff;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        
        /* Shadows */
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, var(--teal-blue) 0%, #0a4b7a 100%);
        color: var(--gray-800);
        line-height: 1.6;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* Background Pattern */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: 
            radial-gradient(circle at 25% 25%, rgba(26, 143, 214, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 75% 75%, rgba(241, 165, 78, 0.08) 0%, transparent 50%);
        pointer-events: none;
        z-index: 0;
    }

    /* Container */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        position: relative;
        z-index: 1;
    }
    
    /* Section Consistency */
    .max-w-5xl {
        max-width: 64rem;
    }

    /* Section Cards - Consistent Width */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 100%;
    }
    
    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Button Styles */
    .btn {
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--curious-blue), var(--picton-blue));
        color: white;
        box-shadow: 0 4px 15px rgba(26, 143, 214, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 143, 214, 0.4);
    }

    .btn-secondary {
        background: var(--white);
        color: var(--curious-blue);
        border: 2px solid var(--curious-blue);
        box-shadow: var(--shadow);
    }

    .btn-secondary:hover {
        background: var(--curious-blue);
        color: white;
        transform: translateY(-2px);
    }

    /* Typography */
    .heading-1 {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(1.75rem, 4vw, 2.5rem);
        font-weight: 600;
        color: var(--teal-blue);
        margin-bottom: 8px;
        letter-spacing: -0.025em;
    }

    .heading-2 {
        font-family: 'Poppins', sans-serif;
        font-size: clamp(1.25rem, 3vw, 1.5rem);
        font-weight: 600;
        color: var(--teal-blue);
        margin-bottom: 16px;
    }

    .text-muted {
        color: var(--gray-500);
        font-size: 14px;
    }

    .text-accent {
        color: var(--sandy-brown);
        font-weight: 500;
    }

    /* Badge System */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-primary {
        background: linear-gradient(135deg, var(--curious-blue), var(--picton-blue));
        color: white;
    }

    .badge-accent {
        background: linear-gradient(135deg, var(--sandy-brown), #f4a261);
        color: white;
    }

    .badge-outline {
        background: transparent;
        border: 1px solid var(--curious-blue);
        color: var(--curious-blue);
    }

    /* Photo Grid - Modern Masonry Style */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 16px;
        margin: 24px 0;
    }

    /* Frame Image Responsive */
    .frame-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .frame-container img {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: contain;
    }

    .photo-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        box-shadow: var(--shadow-lg);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .photo-item:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-xl);
    }

    .photo-container {
        position: relative;
        aspect-ratio: 1;
        overflow: hidden;
        background: var(--gray-100);
    }

    .photo-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        opacity: 1;
    }

    .photo-item:hover .photo-img {
        transform: scale(1.1);
    }

    .photo-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        padding: 16px;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .photo-item:hover .photo-overlay {
        transform: translateY(0);
    }

    /* Section Headers */
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--curious-blue), var(--picton-blue));
        color: white;
        font-size: 16px;
    }

    /* Simple Fade-In Animation for Sections Only */
    .fade-in-delay {
        animation: simpleFadeIn 0.5s ease-out forwards;
        opacity: 1;
    }

    @keyframes simpleFadeIn {
        from {
            opacity: 0.7;
        }
        to {
            opacity: 1;
        }
    }
    
    /* Photo Modal */
    .photo-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .photo-modal.show {
        opacity: 1;
    }
    
    .photo-modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
        margin: 0 20px;
        transform: scale(0.95);
        transition: transform 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .photo-modal.show .photo-modal-content {
        transform: scale(1);
    }

    /* Toast Notifications - Modern */
    .toast-notification {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        transform: translateX(120%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .toast-notification.show {
        transform: translateX(0);
    }
    
    .toast-content {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        background: white;
        color: var(--gray-800);
    }
    
    .toast-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 12px;
        color: white;
    }
    
    .toast-success .toast-icon {
        background: #10b981;
    }
    
    .toast-error .toast-icon {
        background: #ef4444;
    }
    
    .toast-info .toast-icon {
        background: var(--curious-blue);
    }
    
    .toast-warning .toast-icon {
        background: var(--sandy-brown);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }
        
        .photo-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        
        .frame-container {
            max-width: 100%;
        }
        
        .frame-container img {
            max-height: 300px;
        }
        
        .section-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }
        
        .toast-notification {
            left: 16px;
            right: 16px;
            min-width: auto;
            transform: translateY(-120%);
        }
        
        .toast-notification.show {
            transform: translateY(0);
        }
    }

    @media (max-width: 480px) {
        .photo-grid {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 10px;
        }
        
        .frame-container img {
            max-height: 250px;
        }
        
        .glass-card {
            border-radius: 16px;
        }
        
        .btn {
            padding: 10px 20px;
            font-size: 13px;
        }
    }
</style>
