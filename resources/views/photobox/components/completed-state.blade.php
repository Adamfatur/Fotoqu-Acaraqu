{{-- Completed State - Frame is ready, show QR code for gallery access --}}
<div id="completed-state"
    class="h-full hidden flex flex-col items-center justify-center text-center p-4 overflow-y-auto">
    <div class="relative mb-6">
        <div
            class="w-24 h-24 bg-gradient-to-r from-green-400 via-emerald-500 to-teal-500 rounded-full flex items-center justify-center shadow-2xl animate-bounce">
            <i class="fas fa-heart text-white text-3xl"></i>
        </div>
        <div
            class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center animate-ping">
            <i class="fas fa-crown text-white text-sm"></i>
        </div>
    </div>

    <!-- QR Code Section with White Background -->
    <div class="bg-white rounded-xl p-5 mb-6 shadow-lg">
        <!-- Direct QR Code Container - Standard black QR code -->
        <div id="qr-code-display" class="flex items-center justify-center min-h-[300px] min-w-[300px] relative">
            <!-- Static QR code inserted directly -->
            <img id="static-qr-code" src="" style="width:300px; height:300px; display:none;" alt="QR Code">

            <!-- Loading indicator -->
            <div id="qr-loading" class="text-gray-500 flex flex-col items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-gray-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <div class="animate-pulse text-center">Memuat QR Code...</div>
            </div>
        </div>

        <!-- Simple instruction -->
        <div class="text-center mt-2 text-gray-700 font-medium">
            Scan untuk Akses Gallery
        </div>
    </div>

    <!-- Hidden field for gallery URL - not displayed but used by JS -->
    <input type="hidden" id="gallery-url" value="Loading gallery URL...">


    <div class="flex flex-col space-y-3 w-full max-w-sm">
        <button id="print-frame-btn" onclick="printFrame()" style="display: none;"
            class="touch-btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg py-4 text-xl font-medium mb-4 disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="fas fa-print mr-2"></i>
            <span id="print-btn-text">Cetak Frame</span>
        </button>

        <button onclick="resetToWaiting()"
            class="touch-btn bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg py-4 text-xl font-medium">
            <i class="fas fa-home mr-2"></i>
            Kembali ke Menu
        </button>
    </div>

    <div id="auto-return-countdown" class="mt-4 text-white/60 text-sm">
        Otomatis kembali dalam <span id="countdown-timer">3:00</span>
        <button onclick="cancelAutoReturn()" class="ml-2 px-2 py-1 bg-white/20 hover:bg-white/30 rounded-full text-xs">
            <i class="fas fa-pause"></i>
        </button>
    </div>

    {{-- Direct QR Code Failsafe --}}
    <script>
        // Run immediately on page load
        (function () {
            // Function to ensure QR code is visible
            function ensureQRCodeVisible() {
                // Get the QR code image element
                const qrImg = document.getElementById('static-qr-code');
                const qrLoading = document.getElementById('qr-loading');

                if (!qrImg || qrImg.style.display !== 'none') return;

                console.log('🔄 QR code not visible, setting it directly...');

                // Get session code from any available source
                let sessionCode = null;

                // Try DOM data container
                const dataContainer = document.getElementById('session-data-container');
                if (dataContainer && dataContainer.dataset.sessionCode) {
                    sessionCode = dataContainer.dataset.sessionCode;
                }

                // Try session code from URL as fallback
                if (!sessionCode) {
                    const urlParams = new URLSearchParams(window.location.search);
                    sessionCode = urlParams.get('session');
                }

                // Try localStorage as last resort
                if (!sessionCode) {
                    try {
                        const storedSession = localStorage.getItem('fotoku_latest_session');
                        if (storedSession) {
                            const parsed = JSON.parse(storedSession);
                            if (parsed && parsed.session_code) {
                                sessionCode = parsed.session_code;
                            }
                        }
                    } catch (e) { }
                }

                // Generate QR code URL - always use /photobox/gallery/ route for public access
                let qrData;
                if (sessionCode) {
                    qrData = `${window.location.origin}/photobox/gallery/${sessionCode}`;
                } else if (typeof photoboxCode !== 'undefined') {
                    qrData = `${window.location.origin}/photobox/${photoboxCode}/latest`;
                } else {
                    qrData = window.location.origin;
                }

                // Set image source directly
                qrImg.onload = function () {
                    qrImg.style.display = 'block';
                    if (qrLoading) qrLoading.style.display = 'none';
                };

                // Set standard black QR code
                qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}&format=png&t=${Date.now()}`;
            }

            // Try immediately
            ensureQRCodeVisible();

            // And again after 1 second
            setTimeout(ensureQRCodeVisible, 1000);

            // And again after 3 seconds as final attempt
            setTimeout(ensureQRCodeVisible, 3000);

        })();
    </script>

    {{-- QR Code Loader Helper - Will ensure QR code is visible --}}
    <script>
        // This script will run when the completed state is shown
        document.addEventListener('DOMContentLoaded', function () {
            // Check if QR code is loaded after a delay
            setTimeout(function () {
                const qrContainer = document.getElementById('qr-code-display');
                const qrLoading = document.getElementById('qr-loading');
                const galleryUrl = document.getElementById('gallery-url')?.value;

                // If no QR code image is visible but we have a gallery URL
                if (qrContainer && !qrContainer.querySelector('img') && galleryUrl && galleryUrl !== "Loading gallery URL...") {
                    console.log('💢 QR code not loaded yet, forcing reload...');

                    // Force using direct QR API
                    const encodedUrl = encodeURIComponent(galleryUrl);
                    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodedUrl}&t=${new Date().getTime()}`; // Cache busting

                    // Replace content
                    qrContainer.innerHTML = `
                        <img src="${qrUrl}" 
                             alt="QR Code" 
                             style="width:200px; height:200px;"
                             onload="console.log('QR code loaded from helper script'); document.getElementById('qr-loading')?.remove();"
                             onerror="this.onerror=null; this.style.display='none'; document.getElementById('qr-code-display').innerHTML += '<div class=\'text-red-500 text-sm\'>QR tidak dapat dimuat<br><a href=\'' + '${galleryUrl}' + '\' class=\'text-blue-500 underline\' target=\'_blank\'>Buka Gallery</a></div>';">
                    `;

                    // Remove loading indicator if it still exists
                    if (qrLoading) qrLoading.remove();
                }
            }, 2000); // Check after 2 seconds
        });
    </script>
</div>