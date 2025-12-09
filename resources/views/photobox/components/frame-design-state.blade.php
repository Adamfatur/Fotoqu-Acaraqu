{{-- Frame Design Selection State - User selects frame design --}}
<div id="frame-design-state" class="h-full hidden">
    <div class="h-full flex flex-col">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-2">Pilih Desain Frame</h2>
            <p class="text-white/80 text-lg">Pilih desain frame yang Anda inginkan</p>
            <div class="mt-4">
                <span class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-full">
                    <span id="selected-frame-name">Default Frame</span>
                </span>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div id="frame-templates-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 max-w-6xl mx-auto px-4">
                {{-- All frame templates (including default) will be populated here via JavaScript --}}
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-4">
            <button onclick="backToPhotoSelection()" 
                    class="touch-btn bg-gray-600/80 hover:bg-gray-700/80 text-white rounded-xl transition-all duration-200 border border-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </button>
            
            <button id="confirm-frame-btn" onclick="proceedToPhotoFilter()" 
                    class="touch-btn bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-xl hover:from-purple-700 hover:to-pink-600 transition-all duration-200 shadow-lg border border-purple-400">
                <i class="fas fa-arrow-right mr-2"></i>
                {{-- TEMPORARY: Button text changed while photo filter is skipped --}}
                {{-- TODO: Change back to "Lanjut ke Edit Foto" when photo filter is re-enabled --}}
                Proses Frame Sekarang
            </button>
        </div>
    </div>
</div>

    {{-- Frame Preview Modal --}}
    <div id="frame-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4">
        <div class="relative max-w-[92vw] max-h-[92vh]">
            <button id="frame-preview-close" class="absolute -top-3 -right-3 bg-white text-gray-900 rounded-full w-8 h-8 flex items-center justify-center shadow-lg border border-gray-200" aria-label="Tutup Preview">
                <i class="fas fa-times"></i>
            </button>
            <img id="frame-preview-image" src="" alt="Preview Frame" class="max-w-[92vw] max-h-[85vh] w-auto h-auto rounded-xl shadow-2xl object-contain bg-white" />
            <div id="frame-preview-caption" class="text-center text-white/90 mt-3 text-sm"></div>
        </div>
    </div>
