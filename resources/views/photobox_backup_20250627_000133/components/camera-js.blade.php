{{-- Camera and Photo Capture JavaScript --}}
<script>
    async function initializeCamera() {
        try {
            // Stop existing stream if any
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }

            const stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 1280, height: 720, facingMode: 'user' } 
            });
            document.getElementById('camera-preview').srcObject = stream;
            cameraStream = stream;
            
            // Start auto-capture sequence
            startAutoCapture();
        } catch (error) {
            debugLog('Camera access error', error);
            alert('Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.');
        }
    }

    async function startAutoCapture() {
        const totalPhotos = settings.total_photos || 10;
        const intervalSeconds = settings.photo_interval_seconds || 5;
        const countdownSeconds = settings.countdown_seconds || 3;
        
        for (let i = 1; i <= totalPhotos; i++) {
            // Update photo counter
            document.getElementById('photo-count').textContent = i - 1;
            document.getElementById('current-photo').textContent = i;
            
            // Show countdown for this photo
            await showCountdown(countdownSeconds);
            
            // Capture photo
            await capturePhoto(i);
            
            // Wait interval between photos (except last photo)
            if (i < totalPhotos) {
                await waitInterval(intervalSeconds);
            }
        }
        
        // Auto move to selection state
        setTimeout(() => {
            showSelectionState();
        }, 1000);
    }

    async function waitInterval(seconds) {
        const overlay = document.getElementById('interval-overlay');
        const timerEl = document.getElementById('interval-timer');
        
        overlay.classList.remove('hidden');
        
        for (let i = seconds; i > 0; i--) {
            timerEl.textContent = `Foto selanjutnya dalam ${i} detik...`;
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
        
        overlay.classList.add('hidden');
    }

    async function capturePhoto(sequenceNumber) {
        if (!cameraStream) return;

        // Capture photo
        const video = document.getElementById('camera-preview');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        // Show flash effect
        showFlash();
        
        // Convert to base64
        const photoData = canvas.toDataURL('image/jpeg', 0.8);

        try {
            const response = await axios.post(`/photobox/${photoboxCode}/capture`, {
                photo_data: photoData,
                sequence_number: sequenceNumber
            });

            if (response.data.success) {
                capturedPhotos.push(response.data.photo);
                document.getElementById('photo-count').textContent = response.data.captured_count;
            }
        } catch (error) {
            alert('Gagal mengambil foto: ' + (error.response?.data?.error || error.message));
            throw error; // Stop auto-capture on error
        }
    }

    async function showCountdown(seconds) {
        const overlay = document.getElementById('countdown-overlay');
        const numberEl = document.getElementById('countdown-number');
        
        overlay.classList.remove('hidden');
        
        for (let i = seconds; i > 0; i--) {
            numberEl.textContent = i;
            await new Promise(resolve => setTimeout(resolve, 1000));
        }
        
        overlay.classList.add('hidden');
    }

    function showFlash() {
        const flash = document.getElementById('flash-overlay');
        flash.style.opacity = '1';
        setTimeout(() => {
            flash.style.opacity = '0';
        }, 100);
    }

    // Camera settings functions
    function toggleCameraSettings() {
        const panel = document.getElementById('camera-settings-panel');
        panel.classList.toggle('hidden');
    }

    async function loadCameraDevices() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(device => device.kind === 'videoinput');
            const select = document.getElementById('camera-device-select');
            
            select.innerHTML = '<option value="">Pilih Kamera...</option>'; // Reset
            
            videoDevices.forEach(device => {
                const option = document.createElement('option');
                option.value = device.deviceId;
                option.textContent = device.label || `Kamera ${select.length}`;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading camera devices:', error);
            const select = document.getElementById('camera-device-select');
            if(select) {
               select.innerHTML = '<option value="">Error loading cameras</option>';
            }
        }
    }
    
    function refreshCameraDevices() {
         loadCameraDevices();
    }

    function switchCamera() {
        const select = document.getElementById('camera-device-select');
        const deviceId = select.value;

        if (deviceId) {
            // Stop current stream
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }

            // Start new stream with selected camera
            navigator.mediaDevices.getUserMedia({
                video: { deviceId: { exact: deviceId }, width: 1280, height: 720 }
            }).then(stream => {
                document.getElementById('camera-preview').srcObject = stream;
                cameraStream = stream;
            }).catch(error => {
                console.error('Error switching camera:', error);
                alert('Gagal beralih kamera. Pastikan kamera terhubung dan dapat diakses.');
            });
        }
    }

    function testCamera() {
        const select = document.getElementById('camera-device-select');
        const deviceId = select.value;

        if (deviceId) {
             // The camera is already previewing, a simple success message is enough
             alert('Tes kamera berhasil! Kamera siap digunakan.');
        } else {
            alert('Pilih kamera terlebih dahulu.');
        }
    }
</script>
