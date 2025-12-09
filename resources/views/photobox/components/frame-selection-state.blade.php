{{-- Frame Selection State - User chooses frame template --}}
<div id="frame-selection-state" class="h-full hidden">
    <div class="h-full flex flex-col">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-2">Pilih Desain Frame</h2>
            <p class="text-white/80 text-lg">Pilih template frame untuk foto Anda</p>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div id="frame-templates-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4">
                {{-- Frame templates will be populated by JavaScript --}}
            </div>
        </div>

        <div class="mt-6 flex justify-center space-x-4">
            <button onclick="backToPhotoSelection()" 
                    class="touch-btn bg-gray-600/80 hover:bg-gray-700/80 text-white rounded-xl transition-all duration-200 border border-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Pilih Foto
            </button>
            
            <button id="confirm-frame-btn" onclick="proceedToPhotoEdit()" disabled
                    class="touch-btn bg-gradient-to-r from-blue-600 to-indigo-500 text-white rounded-xl hover:from-blue-700 hover:to-indigo-600 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed border border-blue-400">
                <i class="fas fa-arrow-right mr-2"></i>
                Lanjut ke Edit Foto
            </button>
        </div>
    </div>
</div>
