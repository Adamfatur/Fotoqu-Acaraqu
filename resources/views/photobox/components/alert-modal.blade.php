{{-- Reusable Photobox Alert Modal (info/warning/error) --}}
<div id="photobox-alert-modal" class="fixed inset-0 z-[60] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" data-alert-role="backdrop"></div>

    <!-- Modal Content -->
    <div class="relative h-full flex items-center justify-center p-4">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 max-w-lg w-full border border-white/10 shadow-2xl">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div id="photobox-alert-icon" class="text-white/90 text-xl">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 id="photobox-alert-title" class="text-white text-xl font-bold">Info</h3>
                </div>
                <button id="photobox-alert-close" class="text-white/70 hover:text-white text-2xl p-2 hover:bg-white/10 rounded-full transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="photobox-alert-message" class="text-white/90 leading-relaxed"></div>

            <div class="mt-6 flex justify-end gap-2">
                <button id="photobox-alert-confirm" class="touch-btn bg-purple-600/80 hover:bg-purple-700/80 text-white rounded-xl border border-purple-400 px-4 py-2">
                    Oke
                </button>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const modal = document.getElementById('photobox-alert-modal');
            const titleEl = document.getElementById('photobox-alert-title');
            const msgEl = document.getElementById('photobox-alert-message');
            const iconEl = document.getElementById('photobox-alert-icon');
            const btnConfirm = document.getElementById('photobox-alert-confirm');
            const btnClose = document.getElementById('photobox-alert-close');
            const backdrop = modal.querySelector('[data-alert-role="backdrop"]');

            let resolver = null;
            let autoTimer = null;

            function setVariant(variant){
                // icon + accent color by variant
                let icon = 'fa-info-circle';
                if (variant === 'warning') { icon = 'fa-exclamation-triangle'; }
                if (variant === 'error') { icon = 'fa-exclamation-circle'; }
                iconEl.className = 'text-white/90 text-xl';
                iconEl.innerHTML = `<i class="fas ${icon}"></i>`;
            }

            function open({ title = 'Info', message = '', confirmText = 'Oke', variant = 'info', autoCloseMs = 0 } = {}){
                if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
                titleEl.textContent = title;
                // Allow basic text; prevent accidental HTML injection
                msgEl.textContent = '';
                if (typeof message === 'string') {
                    msgEl.textContent = message;
                }
                btnConfirm.textContent = confirmText || 'Oke';
                setVariant(variant);
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                return new Promise((resolve)=>{
                    resolver = resolve;
                    if (autoCloseMs && autoCloseMs > 0) {
                        autoTimer = setTimeout(close, autoCloseMs);
                    }
                });
            }

            function close(){
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                if (autoTimer) { clearTimeout(autoTimer); autoTimer = null; }
                if (typeof resolver === 'function') { const r = resolver; resolver = null; r(); }
            }

            btnConfirm.addEventListener('click', close);
            btnClose.addEventListener('click', close);
            backdrop.addEventListener('click', close);
            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('hidden')) return;
                if (e.key === 'Escape' || e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    close();
                }
            });

            // Expose helpers
            window.showPhotoAlert = function(title, message, opts = {}) {
                // Allow calling with object param only
                if (typeof title === 'object' && title !== null) {
                    return open(title);
                }
                return open({ title, message, ...opts });
            };
            window.closePhotoAlert = close;
        })();
    </script>
</div>
