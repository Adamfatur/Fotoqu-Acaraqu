{{-- Photo Selection JavaScript --}}
<script>
    async function loadPhotos() {
        const grid = document.getElementById('photo-grid');
        debugLog('Starting loadPhotos function', { photoboxCode });
        
        if (!grid) {
            debugLog('ERROR: Photo grid element not found');
            alert('Error: Photo grid element not found. Please refresh the page.');
            return;
        }
        
        grid.innerHTML = '<div class="col-span-full text-center text-white py-8"><i class="fas fa-spinner fa-spin text-4xl mb-4"></i><br>Memuat foto...</div>';
        
        try {
            debugLog('Making API request to get photos');
            
            // Add timeout to the request
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout
            
            const response = await axios.get(`/photobox/${photoboxCode}/photos`, {
                signal: controller.signal,
                timeout: 30000
            });
            
            clearTimeout(timeoutId);
            debugLog('Photos API response received', response.data);
            
            if (response.data.success && response.data.photos && response.data.photos.length > 0) {
                const photos = response.data.photos;
                debugLog('Processing photos', { count: photos.length });
                
                grid.innerHTML = '';

                photos.forEach((photo, index) => {
                    debugLog(`Processing photo ${index + 1}`, photo);
                    
                    const photoDiv = document.createElement('div');
                    photoDiv.className = 'photo-item bg-gray-200 relative cursor-pointer hover:scale-105 transition-transform duration-200 border-2 border-transparent';
                    photoDiv.setAttribute('data-photo-id', photo.id);
                    
                    // Multiple fallback image URLs
                    const photoUrl = photo.url 
                                    || photo.public_url 
                                    || photo.file_path 
                                    || `/storage/photos/${photo.filename}` 
                                    || `/storage/sessions/${photo.session_id}/${photo.filename}` 
                                    || '/images/placeholder-photo.svg';
                    
                    debugLog(`Photo URL for #${photo.sequence_number}`, photoUrl);
                    
                    photoDiv.innerHTML = `
                        <img src="${photoUrl}" 
                             alt="Photo ${photo.sequence_number}" 
                             class="w-full h-full object-cover rounded-lg"
                             onerror="handleImageError(this, ${photo.id}, '${photo.sequence_number}')"
                             onload="handleImageLoad(this)">
                        <div class="absolute top-2 left-2 bg-black/70 text-white text-sm px-2 py-1 rounded">
                            #${photo.sequence_number}
                        </div>
                        <div class="absolute top-2 right-2 selection-indicator hidden">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <div class="text-white text-sm font-medium">Klik untuk pilih</div>
                        </div>
                    `;
                    
                    photoDiv.addEventListener('click', () => togglePhotoSelection(photo.id, photoDiv));
                    grid.appendChild(photoDiv);
                });
                
                // Update capturedPhotos array for selection logic
                capturedPhotos = photos;
                debugLog('Photos loaded successfully', { totalPhotos: photos.length });
                
                // Ensure grid is visible and properly styled
                grid.style.display = 'grid';
                grid.classList.remove('hidden');
                
                // Update UI elements
                updateSelectionUI();
            } else {
                debugLog('No photos found or invalid response', response.data);
                displayNoPhotosMessage(grid);
            }
        } catch (error) {
            debugLog('ERROR loading photos', { 
                message: error.message, 
                code: error.code, 
                response: error.response?.data 
            });
            displayErrorMessage(grid, error);
        }
    }

    function togglePhotoSelection(photoId, element) {
        debugLog('Photo selection toggled', { photoId, selectedPhotos });
        
        // Get max selection from current session with proper parsing
        const maxSelection = currentSession ? parseInt(currentSession.frame_slots) || 4 : 4;
        
        debugLog('Max selection determined', { 
            maxSelection, 
            frameSlots: currentSession?.frame_slots,
            currentSession: currentSession
        });
        
        if (selectedPhotos.includes(photoId)) {
            // Deselect
            selectedPhotos = selectedPhotos.filter(id => id !== photoId);
            element.classList.remove('selected');
            element.style.borderColor = 'transparent';
            
            const indicator = element.querySelector('.selection-indicator');
            if (indicator) indicator.classList.add('hidden');
            
            debugLog('Photo deselected', { photoId, remainingSelected: selectedPhotos });
        } else if (selectedPhotos.length < maxSelection) {
            // Select
            selectedPhotos.push(photoId);
            element.classList.add('selected');
            element.style.borderColor = '#10b981';
            element.style.borderWidth = '3px';
            
            const indicator = element.querySelector('.selection-indicator');
            if (indicator) indicator.classList.remove('hidden');
            
            debugLog('Photo selected', { photoId, totalSelected: selectedPhotos });
            
            // Haptic feedback (if available)
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }
        } else {
            // Show message if trying to select more than allowed
            debugLog('Selection limit reached', { maxSelection, currentSelection: selectedPhotos.length });
            alert(`Maksimal ${maxSelection} foto yang dapat dipilih`);
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
        
        // Ensure frame_slots is treated as integer
        const requiredCount = currentSession ? parseInt(currentSession.frame_slots) || 4 : 4;
        
        if (maxSelectionEl) maxSelectionEl.textContent = requiredCount;
        if (requiredPhotosEl) requiredPhotosEl.textContent = requiredCount;
        
        debugLog('updateSelectionUI called', {
            selectedCount: selectedPhotos.length,
            requiredCount: requiredCount,
            currentSession: currentSession,
            frameSlots: currentSession?.frame_slots,
            frameSlotsParsed: parseInt(currentSession?.frame_slots)
        });
        
        if (confirmBtn) {
            const shouldEnable = selectedPhotos.length === requiredCount && selectedPhotos.length > 0;
            
            debugLog('Button enable check', {
                selectedLength: selectedPhotos.length,
                requiredCount: requiredCount,
                shouldEnable: shouldEnable,
                buttonCurrentlyDisabled: confirmBtn.disabled
            });
            
            if (shouldEnable) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('disabled:opacity-50', 'disabled:cursor-not-allowed');
                confirmBtn.classList.add('hover:from-green-700', 'hover:to-emerald-600');
                debugLog('✅ Confirm button ENABLED');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('disabled:opacity-50', 'disabled:cursor-not-allowed');
                confirmBtn.classList.remove('hover:from-green-700', 'hover:to-emerald-600');
                debugLog('❌ Confirm button DISABLED');
            }
        } else {
            debugLog('❌ Confirm button element not found');
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

    async function confirmSelection() {
        if (selectedPhotos.length === 0) return;

        try {
            showProcessingState();
            
            const response = await axios.post(`/photobox/${photoboxCode}/select`, {
                selected_photos: selectedPhotos
            });

            if (response.data.success) {
                setTimeout(() => {
                    showCompletedState();
                }, 3000);
            }
        } catch (error) {
            alert('Gagal memproses foto: ' + (error.response?.data?.error || error.message));
            showSelectionState();
        }
    }

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

    // Debug function to check button status
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
</script>
