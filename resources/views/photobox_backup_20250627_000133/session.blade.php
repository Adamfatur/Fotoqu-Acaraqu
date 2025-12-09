@extends('photobox.layout')

@section('content')
<div class="max-w-6xl mx-auto w-full" x-data="photoboxApp()" x-init="init()">
    <!-- Header Status -->
    <div class="text-center mb-8">
        <div class="glassmorphism rounded-3xl p-6 text-white">
            <div class="flex items-center justify-center space-x-4 mb-4">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-camera text-3xl"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-3xl font-bold">{{ $photobox->name }}</h1>
                    <p class="text-white/80">{{ $photobox->code }} • Ready untuk foto</p>
                </div>
            </div>
            
            <!-- Session Info -->
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-white/70 text-sm">Customer</p>
                    <p class="font-semibold">{{ $session->customer_name }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-sm">Paket</p>
                    <p class="font-semibold">{{ $session->package->name ?? $session->frame_slots . ' Foto' }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-sm">Status</p>
                    <p class="font-semibold text-green-300" x-text="statusText"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="glassmorphism rounded-3xl p-8 text-white">
        <!-- Welcome Screen -->
        <div x-show="currentStep === 'welcome'" class="text-center">
            <div class="mb-8">
                <div class="w-32 h-32 bg-white/20 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-hand-paper text-6xl"></i>
                </div>
                <h2 class="text-4xl font-bold mb-4">Selamat Datang!</h2>
                <p class="text-xl text-white/80 mb-8">Siap untuk sesi foto Anda? Klik tombol di bawah untuk memulai</p>
                
                <div class="space-y-4">
                    <div class="bg-white/10 rounded-2xl p-6">
                        <h3 class="text-lg font-semibold mb-2">Yang Perlu Anda Ketahui:</h3>
                        <ul class="text-left space-y-2 text-white/80">
                            <li>✨ System akan mengambil {{ $session->package->frame_slots ?? '10' }} foto otomatis</li>
                            <li>📸 Jeda 3 detik antar foto</li>
                            <li>👥 Pastikan semua orang siap di depan kamera</li>
                            <li>😊 Bersiaplah dengan pose dan senyuman terbaik!</li>
                        </ul>
                    </div>
                    
                    <button @click="startSession()" 
                            class="w-full max-w-sm mx-auto bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold py-6 px-8 rounded-2xl text-2xl hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-2xl transform hover:scale-105">
                        <i class="fas fa-play mr-3"></i>
                        Mulai Sesi Foto
                    </button>
                </div>
            </div>
        </div>

        <!-- Countdown Screen -->
        <div x-show="currentStep === 'countdown'" class="text-center">
            <div class="mb-8">
                <h2 class="text-4xl font-bold mb-8">Bersiap-siap!</h2>
                
                <!-- Countdown Circle -->
                <div class="relative w-64 h-64 mx-auto mb-8">
                    <svg class="w-64 h-64 transform -rotate-90" viewBox="0 0 150 150">
                        <circle cx="75" cy="75" r="70" stroke="rgba(255,255,255,0.2)" stroke-width="6" fill="none"></circle>
                        <circle cx="75" cy="75" r="70" stroke="#10b981" stroke-width="6" fill="none" 
                                class="countdown-circle" :style="countdownStyle"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-8xl font-bold" x-text="countdown"></span>
                    </div>
                </div>
                
                <p class="text-2xl text-white/80">Foto akan dimulai dalam <span x-text="countdown"></span> detik</p>
            </div>
        </div>

        <!-- Photo Session Screen -->
        <div x-show="currentStep === 'photo-session'" class="text-center">
            <div class="mb-8">
                <h2 class="text-4xl font-bold mb-4">Sesi Foto Berlangsung</h2>
                <p class="text-xl text-white/80 mb-8">
                    Foto <span x-text="currentPhotoNumber"></span> dari {{ $session->package->frame_slots ?? '10' }}
                </p>
                
                <!-- Progress Bar -->
                <div class="w-full bg-white/20 rounded-full h-4 mb-8">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-4 rounded-full transition-all duration-500"
                         :style="`width: ${(currentPhotoNumber / {{ $session->package->frame_slots ?? '10' }}) * 100}%`"></div>
                </div>

                <!-- Live Camera Preview Placeholder -->
                <div class="bg-black/30 rounded-3xl p-8 mb-8 aspect-video flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-video text-6xl mb-4 text-white/50"></i>
                        <p class="text-white/70">Live Camera Preview</p>
                        <p class="text-sm text-white/50">Foto berikutnya dalam <span x-text="photoCountdown"></span> detik</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Selection Screen -->
        <div x-show="currentStep === 'selection'" class="text-center">
            <div class="mb-8">
                <h2 class="text-4xl font-bold mb-4">Pilih Foto Favorit Anda</h2>
                <p class="text-xl text-white/80 mb-8">
                    Pilih {{ $session->frame_slots }} foto terbaik untuk frame Anda
                </p>
                
                <!-- Photo Grid -->
                <div :class="`photo-grid grid-{{ {{ $session->frame_slots }} }}`">
                    <template x-for="(photo, index) in capturedPhotos" :key="index">
                        <div class="photo-slot" 
                             :class="{ 'filled animate-pulse-glow': selectedPhotos.includes(photo.id) }"
                             @click="togglePhotoSelection(photo)">
                            <img :src="photo.url" :alt="`Foto ${index + 1}`" class="cursor-pointer">
                            <div x-show="selectedPhotos.includes(photo.id)" 
                                 class="absolute top-2 right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white"></i>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="mt-8 flex justify-center space-x-4">
                    <p class="text-lg">
                        Dipilih: <span class="font-bold text-green-300" x-text="selectedPhotos.length"></span> 
                        dari {{ $session->frame_slots }}
                    </p>
                </div>
                
                <button @click="confirmSelection()" 
                        :disabled="selectedPhotos.length !== {{ $session->frame_slots }}"
                        :class="selectedPhotos.length === {{ $session->frame_slots }} ? 
                                'bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700' : 
                                'bg-gray-500 cursor-not-allowed'"
                        class="mt-8 text-white font-bold py-4 px-8 rounded-2xl text-xl transition-all duration-300 shadow-xl">
                    <i class="fas fa-check mr-2"></i>
                    Konfirmasi Pilihan
                </button>
            </div>
        </div>

        <!-- Processing Screen -->
        <div x-show="currentStep === 'processing'" class="text-center">
            <div class="mb-8">
                <div class="w-32 h-32 bg-white/20 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-cog fa-spin text-6xl"></i>
                </div>
                <h2 class="text-4xl font-bold mb-4">Membuat Frame Anda</h2>
                <p class="text-xl text-white/80 mb-8">Mohon tunggu, kami sedang memproses foto Anda...</p>
                
                <div class="space-y-4">
                    <div class="bg-white/10 rounded-2xl p-6">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span>Mengatur layout...</span>
                                <i class="fas fa-check text-green-400" x-show="processingStep >= 1"></i>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Menyusun foto...</span>
                                <i class="fas fa-check text-green-400" x-show="processingStep >= 2"></i>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Menambahkan hiasan...</span>
                                <i class="fas fa-check text-green-400" x-show="processingStep >= 3"></i>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Finalisasi frame...</span>
                                <i class="fas fa-check text-green-400" x-show="processingStep >= 4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completion Screen -->
        <div x-show="currentStep === 'completed'" class="text-center">
            <div class="mb-8">
                <div class="w-32 h-32 bg-green-500/20 rounded-full mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-check-circle text-6xl text-green-400"></i>
                </div>
                <h2 class="text-4xl font-bold mb-4">Selamat! Frame Anda Sudah Siap</h2>
                <p class="text-xl text-white/80 mb-8">
                    Frame foto Anda telah berhasil dibuat dan dikirim ke email
                </p>
                
                <div class="bg-white/10 rounded-2xl p-6 mb-8">
                    <h3 class="text-lg font-semibold mb-4">Apa Selanjutnya?</h3>
                    <ul class="space-y-2 text-white/80">
                        <li>📧 Cek email Anda untuk link download</li>
                        <li>🖨️ Frame bisa langsung dicetak ukuran A5</li>
                        <li>💌 Link download valid selama 30 hari</li>
                        <li>🔄 Admin juga bisa mencetak dari dashboard</li>
                    </ul>
                </div>
                
                <button @click="startNewSession()" 
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold py-4 px-8 rounded-2xl text-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-xl">
                    <i class="fas fa-redo mr-2"></i>
                    Sesi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function photoboxApp() {
    return {
        currentStep: 'welcome', // welcome, countdown, photo-session, selection, processing, completed
        statusText: 'Menunggu',
        countdown: 5,
        currentPhotoNumber: 0,
        photoCountdown: 3,
        capturedPhotos: [],
        selectedPhotos: [],
        processingStep: 0,
        
        init() {
            this.statusText = 'Siap Mulai';
            // Simulate some captured photos for demo
            this.capturedPhotos = Array.from({length: 10}, (_, i) => ({
                id: i + 1,
                url: `https://picsum.photos/300/400?random=${i + 1}`,
                index: i + 1
            }));
        },
        
        get countdownStyle() {
            const progress = ((5 - this.countdown) / 5) * 100;
            const offset = 440 - (440 * progress / 100);
            return `stroke-dashoffset: ${offset}`;
        },
        
        startSession() {
            this.currentStep = 'countdown';
            this.statusText = 'Bersiap';
            this.startCountdown();
        },
        
        startCountdown() {
            const timer = setInterval(() => {
                this.countdown--;
                if (this.countdown <= 0) {
                    clearInterval(timer);
                    this.startPhotoSession();
                }
            }, 1000);
        },
        
        startPhotoSession() {
            this.currentStep = 'photo-session';
            this.statusText = 'Mengambil Foto';
            this.currentPhotoNumber = 1;
            this.simulatePhotoCapture();
        },
        
        simulatePhotoCapture() {
            const totalPhotos = {{ $session->package->frame_slots ?? '10' }};
            const capturePhoto = () => {
                if (this.currentPhotoNumber <= totalPhotos) {
                    // Simulate photo capture
                    this.photoCountdown = 3;
                    
                    const photoTimer = setInterval(() => {
                        this.photoCountdown--;
                        if (this.photoCountdown <= 0) {
                            clearInterval(photoTimer);
                            this.currentPhotoNumber++;
                            
                            if (this.currentPhotoNumber <= totalPhotos) {
                                setTimeout(capturePhoto, 500);
                            } else {
                                this.showPhotoSelection();
                            }
                        }
                    }, 1000);
                }
            };
            
            capturePhoto();
        },
        
        showPhotoSelection() {
            this.currentStep = 'selection';
            this.statusText = 'Pilih Foto';
        },
        
        togglePhotoSelection(photo) {
            const maxSelection = {{ $session->frame_slots }};
            const photoIndex = this.selectedPhotos.indexOf(photo.id);
            
            if (photoIndex > -1) {
                this.selectedPhotos.splice(photoIndex, 1);
            } else if (this.selectedPhotos.length < maxSelection) {
                this.selectedPhotos.push(photo.id);
            }
        },
        
        confirmSelection() {
            if (this.selectedPhotos.length === {{ $session->frame_slots }}) {
                this.processFrame();
            }
        },
        
        processFrame() {
            this.currentStep = 'processing';
            this.statusText = 'Memproses';
            this.processingStep = 0;
            
            // Simulate processing steps
            const steps = [1, 2, 3, 4];
            steps.forEach((step, index) => {
                setTimeout(() => {
                    this.processingStep = step;
                    if (step === 4) {
                        setTimeout(() => this.completeSession(), 2000);
                    }
                }, (index + 1) * 1500);
            });
        },
        
        completeSession() {
            this.currentStep = 'completed';
            this.statusText = 'Selesai';
            
            // Send completion to admin
            this.notifyCompletion();
        },
        
        notifyCompletion() {
            // Send AJAX request to mark session as completed
            axios.post(`/photobox/{{ $photobox->code }}/complete`, {
                session_id: {{ $session->id }},
                selected_photos: this.selectedPhotos
            }).then(response => {
                console.log('Session completed successfully');
            }).catch(error => {
                console.error('Error completing session:', error);
            });
        },
        
        startNewSession() {
            // Redirect to waiting screen or reload
            window.location.reload();
        }
    }
}
</script>
@endsection
