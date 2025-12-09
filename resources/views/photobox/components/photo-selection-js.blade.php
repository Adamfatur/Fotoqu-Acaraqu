{{-- Photo Selection JavaScript --}}
<script>
    // Store all loaded photos for reference
    let allPhotos = [];
    // Track temporary (local) photos captured but not yet mapped to server IDs
    let tempPhotos = [];
    let totalPhotosExpected = (settings && settings.total_photos) ? settings.total_photos : {{ config('fotoku.total_photos', 3) }};

    // Utility: get best display URL for a photo (supports local temp objectUrl)
    function getPhotoDisplayUrl(photo) {
        if (!photo) return '/images/placeholder-photo.svg';
        if (photo.objectUrl) return photo.objectUrl; // local preview
        return photo.url
            || photo.public_url
            || photo.file_path
            || `/storage/photos/${photo.filename || ''}`
            || `/storage/sessions/${photo.session_id || ''}/${photo.filename || ''}`
            || '/images/placeholder-photo.svg';
    }

    // Render the photo grid from a list of photo-like objects
    function renderPhotosGrid(photos) {
        const grid = document.getElementById('photo-grid');
        if (!grid) return;
        grid.innerHTML = '';
        photos.forEach((photo) => {
            const photoDiv = document.createElement('div');
            photoDiv.className = 'photo-item bg-gray-200 relative cursor-pointer hover:scale-105 transition-transform duration-200 border-2 border-transparent';
            const displayId = photo.id ?? photo.serverId ?? `seq-${photo.sequence_number}`;
            photoDiv.setAttribute('data-photo-id', displayId);
            photoDiv.setAttribute('data-seq', String(photo.sequence_number || ''));

            const photoUrl = getPhotoDisplayUrl(photo);
            const uploadingBadge = (photo.temp && !photo.serverId) ? `
                <div class="absolute bottom-2 left-2 bg-yellow-400/90 text-black text-xs px-2 py-0.5 rounded-full shadow flex items-center gap-1">
                    <i class="fas fa-cloud-upload-alt"></i> Mengunggah...
                </div>` : '';

            photoDiv.innerHTML = `
                <img src="${photoUrl}" 
                     alt="Photo ${photo.sequence_number}" 
                     class="w-full h-full object-cover rounded-lg"
                     onerror="handleImageError(this, '${displayId}', '${photo.sequence_number}')"
                     onload="handleImageLoad(this)">
                <div class="absolute top-2 left-2 bg-white/90 text-slate-900 text-sm px-2.5 py-1 rounded-full shadow">
                    #${photo.sequence_number}
                </div>
                <div class="absolute top-2 right-2 selection-indicator hidden">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                ${uploadingBadge}
                <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-200">
                    <div class="h-full flex flex-col justify-center items-center space-y-2">
                        <button onclick="openPhotoPreview('${displayId}'); event.stopPropagation();" 
                                class="bg-white/90 hover:bg-white text-black px-3 py-1 rounded-full text-sm font-medium transition-all">
                            <i class="fas fa-search-plus mr-1"></i>
                            Preview
                        </button>
                        <button onclick="togglePhotoSelection('${displayId}', this.closest('.photo-item')); event.stopPropagation();" 
                                class="bg-green-600/90 hover:bg-green-700 text-white px-3 py-1 rounded-full text-sm font-medium transition-all">
                            <i class="fas fa-check mr-1"></i>
                            Pilih
                        </button>
                    </div>
                </div>
            `;

            photoDiv.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    togglePhotoSelection(displayId, photoDiv);
                }
            });
            grid.appendChild(photoDiv);
        });

        // Ensure grid is visible
        grid.style.display = 'grid';
        grid.classList.remove('hidden');

        // After rendering, attempt auto-selection if applicable
        autoSelectIfNeeded();
    }

    async function loadPhotos() {
        const grid = document.getElementById('photo-grid');
        debugLog('Starting loadPhotos function', { photoboxCode });

        if (!grid) {
            debugLog('ERROR: Photo grid element not found');
            return;
        }

        grid.innerHTML = '<div class="col-span-full text-center text-white py-8"><i class="fas fa-spinner fa-spin text-4xl mb-4"></i><br>Memuat foto...</div>';

        try {
            // 1. Try Memory (capturedPhotos)
            if (Array.isArray(capturedPhotos) && capturedPhotos.length > 0) {
                console.log('📂 Loading photos from memory...');
                tempPhotos = normalizePhotos(capturedPhotos);
                allPhotos = tempPhotos;
                renderPhotosGrid(allPhotos);
            }
            // 2. Try IndexedDB (if memory empty, e.g. refresh)
            else if (window.photoStorage && window.currentSession) {
                console.log('📂 Loading photos from IndexedDB...');
                const dbPhotos = await window.photoStorage.getPhotosBySession(window.currentSession.id);
                if (dbPhotos && dbPhotos.length > 0) {
                    // Re-create object URLs
                    dbPhotos.forEach(p => {
                        if (p.blob && !p.objectUrl) p.objectUrl = URL.createObjectURL(p.blob);
                    });
                    capturedPhotos = dbPhotos; // Restore memory
                    tempPhotos = normalizePhotos(dbPhotos);
                    allPhotos = tempPhotos;
                    renderPhotosGrid(allPhotos);
                }
            }

            // 3. Fetch from Server (Reconcile)
            debugLog('Making API request to get photos');
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000);

            try {
                const response = await axios.get(`/photobox/${photoboxCode}/photos`, {
                    signal: controller.signal
                });
                clearTimeout(timeoutId);

                if (response.data.success && response.data.photos) {
                    allPhotos = reconcileServerPhotos(response.data.photos);
                    renderPhotosGrid(allPhotos);
                    capturedPhotos = allPhotos; // Sync memory
                    updateSelectionUI();
                }
            } catch (netErr) {
                console.warn('⚠️ Server fetch failed, relying on local data:', netErr);
                // If we have local data, we are fine. If not, show error.
                if ((!allPhotos || allPhotos.length === 0) && (!capturedPhotos || capturedPhotos.length === 0)) {
                    throw netErr;
                }
            }

            updateSelectionUI();

        } catch (error) {
            debugLog('ERROR loading photos', error);
            if (!allPhotos || allPhotos.length === 0) {
                displayErrorMessage(grid, error);
            }
        }
    }

    function normalizePhotos(photos) {
        return photos.map(p => ({
            ...p,
            id: p.id,
            serverId: p.serverId || null,
            sequence_number: p.sequence_number || p.sequence || 0,
            url: p.objectUrl || p.url || null,
            temp: Boolean(p.temp || !p.serverId || p.status === 'pending')
        })).sort((a, b) => (a.sequence_number || 0) - (b.sequence_number || 0));
    }

    function togglePhotoSelection(photoId, element) {
        debugLog('Photo selection toggled', { photoId, selectedPhotos });

        // Get max selection from config (3 photos for 6-slot frame)
        const maxSelection = {{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }};

        // Find photo object from allPhotos (support temp id and serverId)
        let photo = null;
        if (typeof photoId === 'string' && photoId.startsWith('seq-')) {
            const seq = photoId.split('seq-')[1];
            photo = allPhotos.find(p => String(p.sequence_number) === String(seq));
        } else {
            photo = allPhotos.find(p => String(p.id) === String(photoId) || String(p.serverId) === String(photoId));
        }

        if (!photo) {
            console.error('Photo not found in allPhotos:', photoId);
            return;
        }

        // Check if photo is already selected (by ID or sequence)
        const isSelected = selectedPhotos.some(p =>
            String(p.id) === String(photo.id) ||
            String(p.serverId) === String(photo.serverId) ||
            (typeof photoId === 'string' && photoId.startsWith('seq-') && String(p.sequence_number) === String(photo.sequence_number))
        );

        if (isSelected) {
            // Deselect
            selectedPhotos = selectedPhotos.filter(p => {
                const matchesId = String(p.id) === String(photo.id);
                const matchesServerId = String(p.serverId) === String(photo.serverId);
                const matchesSeq = String(p.sequence_number) === String(photo.sequence_number);
                return !(matchesId || matchesServerId || matchesSeq);
            });

            if (element) {
                element.classList.remove('selected');
                element.style.borderColor = 'transparent';
                element.style.borderWidth = '2px';
                const indicator = element.querySelector('.selection-indicator');
                if (indicator) indicator.classList.add('hidden');
            }

            debugLog('Photo deselected', { photoId, remainingSelected: selectedPhotos });
        } else if (selectedPhotos.length < maxSelection) {
            // Select - store full photo object
            selectedPhotos.push(photo);

            if (element) {
                element.classList.add('selected');
                element.style.borderColor = '#10b981';
                element.style.borderWidth = '3px';
                const indicator = element.querySelector('.selection-indicator');
                if (indicator) indicator.classList.remove('hidden');
            }

            debugLog('Photo selected', { photoId, totalSelected: selectedPhotos });

            // Haptic feedback (if available)
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        } else {
            // Show message if trying to select more than allowed
            debugLog('Selection limit reached', { maxSelection, currentSelection: selectedPhotos.length });
            if (window.showPhotoAlert) {
                showPhotoAlert({
                    title: 'Batas Pilihan Tercapai',
                    message: `Maksimal ${maxSelection} foto yang dapat dipilih`,
                    variant: 'warning',
                    autoCloseMs: 1800
                });
            }
            return; // Don't update UI if selection failed
        }

        updateSelectionUI();
    }

    function updateSelectionUI() {
        const selectedCountEl = document.getElementById('selected-count');
        const maxSelectionEl = document.getElementById('max-selection');
        const confirmBtn = document.getElementById('confirm-selection-btn');
        const requiredPhotosEl = document.getElementById('required-photos');

        if (selectedCountEl) selectedCountEl.textContent = selectedPhotos.length;

        // Use config value for required photos (3 photos for 6-slot frame)
        const requiredCount = {{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }};

        if (maxSelectionEl) maxSelectionEl.textContent = requiredCount;
        if (requiredPhotosEl) requiredPhotosEl.textContent = requiredCount;

        if (confirmBtn) {
            // Enable button if correct number selected
            // We allow temp photos now, so we just check count
            const shouldEnable = selectedPhotos.length === requiredCount;

            if (shouldEnable) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
                confirmBtn.classList.add('hover:from-green-700', 'hover:to-emerald-600');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
                confirmBtn.classList.remove('hover:from-green-700', 'hover:to-emerald-600');
            }
        }

        // If required count equals total photos expected, auto-select all
        autoSelectIfNeeded();
    }

    // Guard to avoid re-entrancy during auto-selection
    let _autoSelectLock = false;

    // Auto-select all photos when the number of photos taken equals the required selection count
    function autoSelectIfNeeded() {
        try {
            if (_autoSelectLock) return;
            const requiredCount = {{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }};

            if (!allPhotos || !Array.isArray(allPhotos)) return;

            // Only auto-select if we have exactly the number of photos needed (e.g. 3 taken, 3 needed)
            // And if user hasn't manually selected/deselected (heuristic: selectedPhotos is empty)
            if (allPhotos.length === requiredCount && selectedPhotos.length === 0) {
                _autoSelectLock = true;
                console.log('🤖 Auto-selecting all photos...');

                // Select all photos
                allPhotos.forEach(p => {
                    const displayId = p.serverId || p.id || `seq-${p.sequence_number}`;
                    // Find element
                    const el = document.querySelector(`.photo-item[data-photo-id="${displayId}"]`) ||
                        document.querySelector(`.photo-item[data-seq="${String(p.sequence_number)}"]`);

                    togglePhotoSelection(String(displayId), el);
                });

                _autoSelectLock = false;
            }
        } catch (err) {
            _autoSelectLock = false;
            console.error('autoSelectIfNeeded error', err);
        }
    }

    function resetSelection() {
        selectedPhotos = [];
        document.querySelectorAll('.photo-item').forEach(item => {
            item.classList.remove('selected');
            item.style.borderColor = 'transparent';
            item.style.borderWidth = '2px';

            const indicator = item.querySelector('.selection-indicator');
            if (indicator) indicator.classList.add('hidden');
        });
        updateSelectionUI();
    }

    // Helper to wait for uploads
    async function ensurePhotosUploaded(photos) {
        // Check for pending uploads
        const pendingPhotos = photos.filter(p => !p.serverId && (p.temp || p.status === 'pending'));

        if (pendingPhotos.length > 0) {
            console.log('⏳ Waiting for uploads:', pendingPhotos.length);

            // Show waiting modal
            if (window.showPhotoAlert) {
                showPhotoAlert({
                    title: 'Mengunggah Foto...',
                    message: `Mohon tunggu, sedang mengunggah ${pendingPhotos.length} foto pilihan ke server...`,
                    variant: 'info',
                    autoCloseMs: 0 // Persistent
                });
            }

            // Wait loop
            const maxWait = 30000; // 30s max wait
            const start = Date.now();

            while (pendingPhotos.some(p => !p.serverId)) {
                if (Date.now() - start > maxWait) {
                    if (window.showPhotoAlert) {
                        showPhotoAlert({
                            title: 'Koneksi Lambat',
                            message: 'Gagal mengunggah foto tepat waktu. Periksa koneksi internet Anda.',
                            variant: 'error'
                        });
                    }
                    return false;
                }

                // Check if any updated in allPhotos (reconciliation happens via event listener)
                // We need to refresh our 'pendingPhotos' reference from 'allPhotos'
                const stillPending = [];
                for (let p of pendingPhotos) {
                    const current = allPhotos.find(ap => ap.sequence_number == p.sequence_number);
                    if (current && !current.serverId) stillPending.push(current);
                }

                if (stillPending.length === 0) break; // All done

                await new Promise(r => setTimeout(r, 1000));
            }

            // Close alert if it was open (by showing processing state next or returning true)
            return true;
        }
        return true;
    }

    async function confirmSelection() {
        if (selectedPhotos.length === 0) return;

        // Wait for uploads
        const uploadsComplete = await ensurePhotosUploaded(selectedPhotos);
        if (!uploadsComplete) return;

        try {
            showProcessingState();

            // Re-map IDs to ensure we have server IDs
            const photoIds = selectedPhotos.map(p => {
                // Find latest version in allPhotos to get serverId
                const latest = allPhotos.find(ap => ap.sequence_number == p.sequence_number);
                return latest ? latest.serverId : null;
            }).filter(Boolean);

            if (photoIds.length !== selectedPhotos.length) {
                throw new Error('Data foto tidak sinkron. Silakan refresh halaman.');
            }

            const response = await axios.post(`/photobox/${photoboxCode}/select`, {
                selected_photos: photoIds
            });

            if (response.data.success) {
                // Clear local storage for this session as it is complete
                if (window.photoStorage && window.currentSession) {
                    window.photoStorage.clearSession(window.currentSession.id);
                }

                setTimeout(() => {
                    showCompletedState();
                }, 3000);
            }
        } catch (error) {
            console.error('Selection failed:', error);
            if (window.showPhotoAlert) {
                showPhotoAlert({
                    title: 'Gagal Memproses',
                    message: (error.response?.data?.error || error.message),
                    variant: 'error'
                }).then(() => { showSelectionState(); });
            } else {
                showSelectionState();
            }
        }
    }

    // Navigate to frame design state
    async function proceedToFrameDesign() {
        if (selectedPhotos.length === 0) {
            if (window.showPhotoAlert) {
                showPhotoAlert({
                    title: 'Belum Ada Pilihan',
                    message: 'Silakan pilih foto terlebih dahulu.',
                    variant: 'info',
                    autoCloseMs: 1600
                });
            }
            return;
        }

        const requiredCount = {{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }};
        if (selectedPhotos.length !== requiredCount) {
            if (window.showPhotoAlert) {
                showPhotoAlert({
                    title: 'Jumlah Pilihan Belum Tepat',
                    message: `Silakan pilih ${requiredCount} foto sesuai kebutuhan frame.`,
                    variant: 'warning'
                });
            }
            return;
        }

        // Wait for uploads before proceeding to frame design
        // This ensures frame design state has valid server IDs to work with
        const uploadsComplete = await ensurePhotosUploaded(selectedPhotos);
        if (!uploadsComplete) return;

        console.log('Proceeding to frame design with selected photos:', selectedPhotos);

        // Debug: Ensure data is correctly transferred
        console.log('=== DATA TRANSFER DEBUG ===');
        console.log('selectedPhotos type:', typeof selectedPhotos);
        console.log('selectedPhotos length:', selectedPhotos.length);
        console.log('selectedPhotos content:', selectedPhotos);
        selectedPhotos.forEach((photo, index) => {
            console.log(`Photo ${index + 1}:`, photo);
        });

        hideAllStates();
        showState('frame-design-state');

        // Use safe initialization with better error handling
        if (typeof window.safeInitializeFrameDesign === 'function') {
            window.safeInitializeFrameDesign();
        } else if (typeof initializeFrameDesign === 'function') {
            initializeFrameDesign();
        } else {
            console.warn('🔄 Frame design functions not available yet, retrying...');
            let retryCount = 0;
            const maxRetries = 10;

            const retryInitialization = () => {
                retryCount++;
                if (typeof window.safeInitializeFrameDesign === 'function') {
                    console.log('✅ Found safeInitializeFrameDesign, calling it');
                    window.safeInitializeFrameDesign();
                } else if (typeof initializeFrameDesign === 'function') {
                    console.log('✅ Found initializeFrameDesign, calling it');
                    initializeFrameDesign();
                } else if (retryCount < maxRetries) {
                    console.log(`⏳ Retry ${retryCount}/${maxRetries} in 100ms...`);
                    setTimeout(retryInitialization, 100);
                } else {
                    console.error('❌ Failed to initialize frame design after maximum retries');
                }
            };

            setTimeout(retryInitialization, 100);
        }
    }

    // Debug function for photo selection testing
    function debugPhotoSelection() {
        console.log('=== PHOTO SELECTION DEBUG ===');
        console.log('All loaded photos:', allPhotos);
        console.log('Selected photos:', selectedPhotos);
        console.log('Current session:', currentSession);

        if (selectedPhotos.length > 0) {
            console.log('First selected photo structure:', selectedPhotos[0]);
        }

        return {
            allPhotos,
            selectedPhotos,
            currentSession
        };
    }

    // Expose for console testing
    window.debugPhotoSelection = debugPhotoSelection;

    // Helper functions for photo loading
    function displayNoPhotosMessage(grid) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="bg-yellow-500/20 text-yellow-200 rounded-xl p-6 border border-yellow-400/30 max-w-md mx-auto">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Tidak Ada Foto</h3>
                    <p class="mb-4">Tidak ada foto yang tersedia untuk dipilih</p>
                    <button onclick="showCaptureState()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-camera mr-2"></i>Ambil Foto Lagi
                    </button>
                </div>
            </div>
        `;
    }

    function displayErrorMessage(grid, error) {
        const errorMessage = error.code === 'ECONNABORTED' ? 'Request timeout' :
            (error.response?.data?.error || error.message || 'Unknown error');

        grid.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="bg-red-500/20 text-red-200 rounded-xl p-6 border border-red-400/30 max-w-md mx-auto">
                    <i class="fas fa-exclamation-circle text-4xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Error Memuat Foto</h3>
                    <p class="mb-4 text-sm">Terjadi kesalahan: ${errorMessage}</p>
                    <div class="space-x-2">
                        <button onclick="loadPhotos()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>Coba Lagi
                        </button>
                        <button onclick="showCaptureState()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-camera mr-2"></i>Ambil Foto Baru
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper function for image error handling
    function handleImageError(img, photoId, sequenceNumber) {
        console.warn('Image load error for photo:', photoId, 'sequence:', sequenceNumber);

        // Try different fallback URLs
        const fallbackUrls = [
            '/images/placeholder-photo.svg',
            '/images/placeholder-photo.jpg',
            '/images/no-image.png',
            'data:image/svg+xml;base64,' + btoa(`
                <svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300">
                    <rect width="400" height="300" fill="#f3f4f6"/>
                    <text x="50%" y="50%" text-anchor="middle" fill="#6b7280" font-family="Arial" font-size="16">
                        Photo #${sequenceNumber}
                    </text>
                    <text x="50%" y="65%" text-anchor="middle" fill="#9ca3af" font-family="Arial" font-size="12">
                        Image not available
                    </text>
                </svg>
            `)
        ];

        const currentSrc = img.src;
        let nextFallback = null;

        for (let i = 0; i < fallbackUrls.length; i++) {
            if (!currentSrc.includes(fallbackUrls[i])) {
                nextFallback = fallbackUrls[i];
                break;
            }
        }

        if (nextFallback) {
            img.src = nextFallback;
        } else {
            // Last resort: create a placeholder div
            const parentDiv = img.parentElement;
            parentDiv.innerHTML = `
                <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-600">
                    <div class="text-center">
                        <i class="fas fa-image text-3xl mb-2"></i>
                        <div class="text-sm">Photo #${sequenceNumber}</div>
                        <div class="text-xs">Not available</div>
                    </div>
                </div>
                <div class="absolute top-2 left-2 bg-black/70 text-white text-sm px-2 py-1 rounded">
                    #${sequenceNumber}
                </div>
                <div class="absolute top-2 right-2 selection-indicator hidden">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            `;
        }
    }

    function handleImageLoad(img) {
        console.log('Image loaded successfully:', img.src);
        img.style.opacity = '1';
        img.onerror = null; // Prevent infinite loop
    }

    // Debug function to check button status - now active in production
    function debugButtonStatus() {
        const confirmBtn = document.getElementById('confirm-selection-btn');
        const selectedCountEl = document.getElementById('selected-count');
        const maxSelectionEl = document.getElementById('max-selection');

        console.log('=== BUTTON DEBUG STATUS ===');
        console.log('Button element found:', !!confirmBtn);
        console.log('Button disabled:', confirmBtn?.disabled);
        console.log('Button classes:', confirmBtn?.className);
        console.log('Selected photos:', selectedPhotos);
        console.log('Selected count:', selectedPhotos.length);
        console.log('Current session:', currentSession);
        console.log('Frame slots (raw):', currentSession?.frame_slots);
        console.log('Frame slots (parsed):', parseInt(currentSession?.frame_slots));
        console.log('Selected count element text:', selectedCountEl?.textContent);
        console.log('Max selection element text:', maxSelectionEl?.textContent);
        console.log('Should enable button:', selectedPhotos.length === parseInt(currentSession?.frame_slots || 4) && selectedPhotos.length > 0);
        console.log('========================');
    }

    // === PHOTO PREVIEW FUNCTIONS ===
    let currentPreviewIndex = 0;
    let currentPreviewPhotos = [];

    function openPhotoPreview(photoId) {
        console.log('🔍 Opening photo preview for ID:', photoId);

        const modal = document.getElementById('photo-preview-modal');
        const previewImage = document.getElementById('preview-photo-image');
        const previewNumber = document.getElementById('preview-photo-number');

        if (!modal || !previewImage || !previewNumber) {
            console.error('❌ Preview modal elements not found');
            return;
        }

        // Normalize lookups: support id, serverId, and seq-* token
        let targetPhoto = null;
        if (typeof photoId === 'string' && photoId.startsWith('seq-')) {
            const seq = photoId.split('seq-')[1];
            targetPhoto = allPhotos.find(p => String(p.sequence_number) === String(seq));
        } else {
            targetPhoto = allPhotos.find(p => String(p.id) === String(photoId) || String(p.serverId) === String(photoId));
        }

        if (!targetPhoto) {
            console.error('❌ Photo not found in allPhotos array:', photoId);
            return;
        }

        // Set current preview data
        currentPreviewPhotos = allPhotos;
        // Prefer index by id/serverId first
        let idx = -1;
        if (typeof photoId === 'string' && photoId.startsWith('seq-')) {
            idx = allPhotos.findIndex(p => String(p.sequence_number) === String(targetPhoto.sequence_number));
        } else {
            idx = allPhotos.findIndex(p => String(p.id) === String(photoId) || String(p.serverId) === String(photoId));
        }
        // Fallback to sequence_number match
        if (idx === -1 && targetPhoto.sequence_number != null) {
            idx = allPhotos.findIndex(p => String(p.sequence_number) === String(targetPhoto.sequence_number));
        }
        // Final fallback: direct reference or first photo
        if (idx === -1) {
            const refIdx = allPhotos.indexOf(targetPhoto);
            idx = refIdx !== -1 ? refIdx : 0;
        }
        currentPreviewIndex = idx;

        // Update preview content
        updatePreviewContent();

        // Show modal
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scroll

        console.log('✅ Photo preview opened:', { photoId, index: currentPreviewIndex });
    }

    function closePhotoPreview() {
        console.log('❌ Closing photo preview');

        const modal = document.getElementById('photo-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = ''; // Restore scroll
        }
    }

    function updatePreviewContent() {
        if (currentPreviewIndex < 0 || currentPreviewIndex >= currentPreviewPhotos.length) {
            console.error('❌ Invalid preview index:', currentPreviewIndex);
            return;
        }

        const photo = currentPreviewPhotos[currentPreviewIndex];
        const previewImage = document.getElementById('preview-photo-image');
        const previewNumber = document.getElementById('preview-photo-number');
        const selectBtn = document.getElementById('preview-select-btn');
        const selectText = document.getElementById('preview-select-text');
        const prevBtn = document.getElementById('preview-prev-btn');
        const nextBtn = document.getElementById('preview-next-btn');

        if (!photo) {
            console.error('❌ Photo not found at index:', currentPreviewIndex);
            return;
        }

        // Get photo URL with fallbacks
        const photoUrl = getPhotoDisplayUrl(photo);

        // Update preview image
        if (previewImage) {
            previewImage.src = photoUrl;
            previewImage.alt = `Photo ${photo.sequence_number}`;
        }

        // Update photo number
        if (previewNumber) {
            previewNumber.textContent = photo.sequence_number;
        }

        // Update selection button state (support id/serverId/sequence fallback)
        const isSelected = selectedPhotos.some(p =>
            String(p.id) === String(photo.id) ||
            String(p.serverId) === String(photo.serverId) ||
            String(p.sequence_number) === String(photo.sequence_number)
        );
        if (selectBtn && selectText) {
            if (isSelected) {
                selectBtn.className = 'touch-btn bg-red-600/80 hover:bg-red-700/80 text-white rounded-xl border border-red-400';
                selectText.innerHTML = '<i class="fas fa-times mr-2"></i>Batalkan Pilihan';
            } else {
                selectBtn.className = 'touch-btn bg-green-600/80 hover:bg-green-700/80 text-white rounded-xl border border-green-400';
                selectText.innerHTML = '<i class="fas fa-check mr-2"></i>Pilih Foto';
            }
        }

        // Update navigation buttons
        if (prevBtn) {
            prevBtn.disabled = currentPreviewIndex === 0;
            prevBtn.classList.toggle('opacity-50', currentPreviewIndex === 0);
        }

        if (nextBtn) {
            nextBtn.disabled = currentPreviewIndex === currentPreviewPhotos.length - 1;
            nextBtn.classList.toggle('opacity-50', currentPreviewIndex === currentPreviewPhotos.length - 1);
        }

        console.log('✅ Preview content updated:', {
            photoId: photo.id,
            sequence: photo.sequence_number,
            isSelected: isSelected,
            index: currentPreviewIndex,
            total: currentPreviewPhotos.length
        });
    }

    function previewPreviousPhoto() {
        if (currentPreviewIndex > 0) {
            currentPreviewIndex--;
            updatePreviewContent();
            console.log('⬅️ Preview previous photo:', currentPreviewIndex);
        }
    }

    function previewNextPhoto() {
        if (currentPreviewIndex < currentPreviewPhotos.length - 1) {
            currentPreviewIndex++;
            updatePreviewContent();
            console.log('➡️ Preview next photo:', currentPreviewIndex);
        }
    }

    function togglePreviewPhotoSelection() {
        if (currentPreviewIndex < 0 || currentPreviewIndex >= currentPreviewPhotos.length) {
            console.error('❌ Invalid preview index for selection:', currentPreviewIndex);
            return;
        }

        const photo = currentPreviewPhotos[currentPreviewIndex];
        // Determine the best DOM identifier for the tile
        const queryId = (photo.id != null ? photo.id : (photo.serverId != null ? photo.serverId : `seq-${photo.sequence_number}`));
        let photoElement = document.querySelector(`[data-photo-id="${queryId}"]`);
        if (!photoElement && photo.sequence_number != null) {
            photoElement = document.querySelector(`.photo-item[data-seq="${String(photo.sequence_number)}"]`);
        }

        if (photoElement) {
            togglePhotoSelection(queryId, photoElement);
            updatePreviewContent(); // Update button state
            console.log('🔄 Photo selection toggled from preview:', photo.id);
        } else {
            console.error('❌ Photo element not found for selection:', photo.id);
        }
    }

    // Add global functions for preview
    window.openPhotoPreview = openPhotoPreview;
    window.closePhotoPreview = closePhotoPreview;
    window.previewPreviousPhoto = previewPreviousPhoto;
    window.previewNextPhoto = previewNextPhoto;
    window.togglePreviewPhotoSelection = togglePreviewPhotoSelection;

    // Add keyboard navigation for preview modal
    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('photo-preview-modal');
        if (!modal || modal.classList.contains('hidden')) {
            return; // Modal is not open
        }

        switch (e.key) {
            case 'Escape':
                closePhotoPreview();
                e.preventDefault();
                break;
            case 'ArrowLeft':
                previewPreviousPhoto();
                e.preventDefault();
                break;
            case 'ArrowRight':
                previewNextPhoto();
                e.preventDefault();
                break;
            case ' ': // Spacebar
            case 'Enter':
                togglePreviewPhotoSelection();
                e.preventDefault();
                break;
        }
    });

    // Export helpers for other modules
    window.renderPhotosGrid = renderPhotosGrid;

    // Listen for upload progress events to update UI in real-time
    document.addEventListener('fotoku:photoUploaded', (e) => {
        console.log('🔔 Event received: fotoku:photoUploaded', e.detail);
        const { sequence_number, serverId, serverData, localId } = e.detail;

        // 1. Update allPhotos array
        const photoIndex = allPhotos.findIndex(p => p.sequence_number == sequence_number);
        if (photoIndex !== -1) {
            allPhotos[photoIndex].serverId = serverId;
            allPhotos[photoIndex].serverData = serverData;
            allPhotos[photoIndex].temp = false; // It's no longer just a temp local file
            console.log(`✅ Updated allPhotos[${photoIndex}] with serverId: ${serverId}`);
        }

        // 2. Update capturedPhotos (memory source)
        if (window.capturedPhotos) {
            const capIndex = window.capturedPhotos.findIndex(p => p.sequence_number == sequence_number);
            if (capIndex !== -1) {
                window.capturedPhotos[capIndex].serverId = serverId;
                window.capturedPhotos[capIndex].serverData = serverData;
            }
        }

        // 3. Update UI
        // Find the grid item
        const displayId = localId || `seq-${sequence_number}`;
        let item = document.querySelector(`.photo-item[data-photo-id="${displayId}"]`);
        if (!item) {
            item = document.querySelector(`.photo-item[data-seq="${sequence_number}"]`);
        }

        if (item) {
            // Remove uploading badge
            const badge = item.querySelector('.bg-yellow-400\\/90'); // Escaped slash for selector
            if (badge) badge.remove();

            // Update data attribute to serverId for future reference
            item.setAttribute('data-photo-id', serverId);

            // Add success indicator briefly
            const successBadge = document.createElement('div');
            successBadge.className = 'absolute bottom-2 left-2 bg-green-500/90 text-white text-xs px-2 py-0.5 rounded-full shadow flex items-center gap-1 fade-out';
            successBadge.innerHTML = '<i class="fas fa-check"></i> Terunggah';
            item.appendChild(successBadge);

            setTimeout(() => {
                if (successBadge.parentNode) successBadge.remove();
            }, 2000);
        }
    });

    document.addEventListener('fotoku:uploadStart', (e) => {
        console.log('🔔 Event received: fotoku:uploadStart', e.detail);
        // Optional: Add uploading badge if missing
    });

</script>