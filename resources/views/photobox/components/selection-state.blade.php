{{-- Selection State - User selects best photos for frame --}}
<div id="selection-state" class="h-full hidden">
    <div class="h-full flex flex-col">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-2">Pilih Foto Terbaik</h2>
            <p class="text-white/80 text-lg">Pilih <span id="required-photos">{{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }}</span> foto untuk frame Anda (dari {{ config('fotoku.total_photos', 3) }} foto yang diambil)</p>
            <div class="mt-4">
                <span class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-full">
                    <span id="selected-count">0</span> / <span id="max-selection">{{ config('fotoku.frame_templates.fotostrip.user_selection', 3) }}</span> terpilih
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div id="photo-grid" class="photo-grid">
                {{-- Photos will be populated by JavaScript --}}
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-4">
            <button onclick="resetSelection()" 
                    class="touch-btn bg-gray-600/80 hover:bg-gray-700/80 text-white rounded-xl transition-all duration-200 border border-gray-500">
                <i class="fas fa-undo mr-2"></i>
                Reset Pilihan
            </button>
            
            <button onclick="debugButtonStatus()" 
                    class="touch-btn bg-purple-600/80 hover:bg-purple-700/80 text-white rounded-xl transition-all duration-200 border border-purple-500"
                    style="display: none;">
                <i class="fas fa-bug mr-2"></i>
                Debug
            </button>
            
            <button id="confirm-selection-btn" onclick="proceedToFrameDesign()" disabled
                    class="touch-btn bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl hover:from-green-700 hover:to-emerald-600 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed border border-green-400">
                <i class="fas fa-arrow-right mr-2"></i>
                Lanjut Pilih Frame
            </button>
        </div>
    </div>
</div>

{{-- Photo Preview Modal --}}
<div id="photo-preview-modal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closePhotoPreview()"></div>
    
    <!-- Modal Content -->
    <div class="relative h-full flex items-center justify-center p-4">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 max-w-4xl max-h-[90vh] w-full flex flex-col">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <div class="text-white">
                    <h3 class="text-xl font-bold">Preview Foto</h3>
                    <p class="text-white/70 text-sm">Foto #<span id="preview-photo-number">1</span></p>
                </div>
                <button onclick="closePhotoPreview()" 
                        class="text-white/70 hover:text-white text-2xl p-2 hover:bg-white/10 rounded-full transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Photo Container -->
            <div class="flex-1 flex items-center justify-center mb-4 bg-black/20 rounded-xl overflow-hidden">
                <img id="preview-photo-image" 
                     src="" 
                     alt="Preview" 
                     class="max-w-full max-h-full object-contain rounded-lg">
            </div>
            
            <!-- Navigation and Actions -->
            <div class="flex justify-between items-center">
                <!-- Navigation -->
                <div class="flex space-x-2">
                    <button id="preview-prev-btn" onclick="previewPreviousPhoto()" 
                            class="touch-btn bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20">
                        <i class="fas fa-chevron-left mr-2"></i>
                        Sebelumnya
                    </button>
                    <button id="preview-next-btn" onclick="previewNextPhoto()" 
                            class="touch-btn bg-white/10 hover:bg-white/20 text-white rounded-xl border border-white/20">
                        Berikutnya
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
                
                <!-- Photo Actions -->
                <div class="flex space-x-2">
                    <button id="preview-select-btn" onclick="togglePreviewPhotoSelection()" 
                            class="touch-btn bg-green-600/80 hover:bg-green-700/80 text-white rounded-xl border border-green-400">
                        <span id="preview-select-text"><i class="fas fa-check mr-2"></i>Pilih Foto</span>
                    </button>
                    <button onclick="closePhotoPreview()" 
                            class="touch-btn bg-gray-600/80 hover:bg-gray-700/80 text-white rounded-xl border border-gray-500">
                        <i class="fas fa-times mr-2"></i>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
