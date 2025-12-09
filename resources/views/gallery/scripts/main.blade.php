{{-- Modern Gallery Scripts --}}
<script>
// Modern Toast System
function showToast(message, type = 'info') {
    // Remove existing toasts
    document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    
    const iconMap = {
        info: 'info-circle',
        success: 'check-circle', 
        error: 'exclamation-circle',
        warning: 'exclamation-triangle'
    };
    
    toast.innerHTML = `
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-${iconMap[type]}"></i>
            </div>
            <div>
                <div class="font-medium">${message}</div>
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 4000);
}

// Simplified image handling without animations
function setupImageHandling() {
    const images = document.querySelectorAll('.photo-img');
    
    images.forEach((img) => {
        img.addEventListener('error', function() {
            this.src = '/images/placeholder-photo.png';
        });
    });
}

// Enhanced Photo Viewer
function openPhotoViewer(photoUrl, photoIndex) {
    // Hapus modal lama jika ada
    const existingModal = document.querySelector('.photo-modal');
    if (existingModal) {
        document.body.removeChild(existingModal);
    }
    
    // Buat modal baru
    const modal = document.createElement('div');
    modal.className = 'photo-modal';
    
    modal.innerHTML = `
        <div class="photo-modal-content">
            <button onclick="closePhotoViewer(this)" class="absolute top-4 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-all z-10">
                <i class="fas fa-times"></i>
            </button>
            <div class="bg-white rounded-xl p-3 shadow-2xl">
                <img src="${photoUrl}" 
                     class="max-w-full max-h-[80vh] object-contain rounded-lg mx-auto block" 
                     alt="Foto ${photoIndex + 1}">
            </div>
            <div class="text-center mt-4 flex items-center justify-center gap-3">
                <div class="w-2 h-2 bg-white rounded-full"></div>
                <p class="text-white/90 text-sm">Foto ${photoIndex + 1}</p>
                <div class="w-2 h-2 bg-white rounded-full"></div>
            </div>
        </div>
    `;
    
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closePhotoViewer(modal.querySelector('button'));
        }
    });
    
    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

function closePhotoViewer(button) {
    const modal = button.closest('.photo-modal');
    modal.classList.remove('show');
    setTimeout(() => {
        document.body.removeChild(modal);
        document.body.style.overflow = 'auto';
    }, 300);
}

// Share functionality
function shareToSocial() {
    const galleryUrl = window.location.href;
    const message = `🎉 Lihat hasil foto sesi FOTOKU saya! ✨\n\n📸 ${galleryUrl}\n\n#FOTOKU #PhotoSession`;
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
    showToast('Membuka WhatsApp untuk berbagi gallery', 'info');
}

async function copyGalleryLink() {
    try {
        await navigator.clipboard.writeText(window.location.href);
        showToast('Link gallery berhasil disalin!', 'success');
    } catch (error) {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = window.location.href;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showToast('Link gallery berhasil disalin!', 'success');
    }
}

// UI Helper functions
function showLoadingState(buttonId) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    button.disabled = true;
    const originalText = button.getAttribute('data-original-text') || button.textContent;
    button.setAttribute('data-original-text', originalText);
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
}

function hideLoadingState(buttonId) {
    const button = document.getElementById(buttonId);
    if (!button) return;
    
    button.disabled = false;
    const originalText = button.getAttribute('data-original-text');
    if (originalText) {
        button.innerHTML = originalText;
    }
}

// Keyboard controls for photo viewer
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.querySelector('.photo-modal');
        if (modal) {
            const closeButton = modal.querySelector('button');
            closePhotoViewer(closeButton);
        }
    }
});

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup image handling
    setupImageHandling();
    
    // Add simple fade-in for sections
    document.querySelectorAll('.fade-in-delay').forEach((el, index) => {
        el.style.animationDelay = `${0.1 + (index * 0.1)}s`;
    });
    
    // Welcome message with delay
    setTimeout(() => {
        showToast('Selamat datang di Gallery FOTOKU! 📸', 'info');
    }, 1000);
});


</script>
