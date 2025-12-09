{{-- Selection State - User selects best photos for frame --}}
<div id="selection-state" class="h-full hidden">
    <div class="h-full flex flex-col">
        <div class="mb-6 text-center">
            <h2 class="text-3xl font-bold text-white mb-2">Pilih Foto Terbaik</h2>
            <p class="text-white/80 text-lg">Pilih <span id="required-photos">{{ $activeSession ? $activeSession->frame_slots : 4 }}</span> foto untuk frame Anda</p>
            <div class="mt-4">
                <span class="bg-white/10 backdrop-blur-md text-white px-4 py-2 rounded-full">
                    <span id="selected-count">0</span> / <span id="max-selection">{{ $activeSession ? $activeSession->frame_slots : 4 }}</span> terpilih
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
                    style="display: {{ config('app.debug') ? 'inline-flex' : 'none' }}">
                <i class="fas fa-bug mr-2"></i>
                Debug
            </button>
            
            <button id="confirm-selection-btn" onclick="confirmSelection()" disabled
                    class="touch-btn bg-gradient-to-r from-green-600 to-emerald-500 text-white rounded-xl hover:from-green-700 hover:to-emerald-600 transition-all duration-200 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed border border-green-400">
                <i class="fas fa-check mr-2"></i>
                Konfirmasi Pilihan
            </button>
        </div>
    </div>
</div>
