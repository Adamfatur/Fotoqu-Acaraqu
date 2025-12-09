{{-- Photo Filter State - Final Version with Vertical Space Distribution --}}
<div id="photo-filter-state" class="h-screen hidden flex flex-col bg-gradient-to-br from-purple-900/20 via-blue-900/20 to-indigo-800/20 text-sm">
    {{-- Header Section --}}
    <div class="flex-shrink-0 bg-black/10 backdrop-blur-sm border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 py-1.5">
            <div class="flex items-center justify-between">
                <div class="text-left">
                    <h2 class="text-lg lg:text-xl font-bold text-white">✨ Edit Foto</h2>
                    <p class="text-white/70 text-xs">Pilih filter untuk foto Anda</p>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button id="apply-to-all-btn" onclick="toggleApplyToAll()"
                            class="bg-blue-600/80 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md transition-all duration-200 border border-blue-400 font-medium">
                        <i class="fas fa-magic mr-1.5"></i>
                        <span id="apply-to-all-text">Filter ke Semua</span>
                    </button>
                    
                    <button onclick="resetAllFilters()"
                            class="bg-red-600/80 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition-all duration-200 border border-red-500 font-medium">
                        <i class="fas fa-undo mr-1.5"></i>
                        Reset Semua
                    </button>
                    
                    <button id="confirm-filter-btn" onclick="proceedToProcessing()"
                            class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-4 py-1.5 rounded-md hover:from-green-700 hover:to-emerald-600 transition-all duration-200 shadow-lg border border-green-400 font-bold pulse-animation">
                        <i class="fas fa-check mr-1.5"></i>
                        Lanjutkan
                    </button>

                    <button onclick="debugSessionStatus()"
                            class="bg-yellow-600/80 hover:bg-yellow-700 text-white px-3 py-1.5 rounded-md transition-all duration-200 border border-yellow-500 font-medium"
                            style="display: none;">
                        <i class="fas fa-bug mr-1.5"></i>
                        Debug Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="flex-1 overflow-hidden">
        <div class="max-w-7xl mx-auto h-full flex gap-3 px-4 py-2">
            {{-- Photo Preview Section (No changes here) --}}
            <div class="flex-1 min-w-0">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-2 h-full flex flex-col border border-white/20 shadow-xl">
                    <div class="flex justify-between items-center mb-2">
                        <button onclick="previousPhoto()" id="prev-photo-btn"
                                class="w-9 h-9 bg-white/20 hover:bg-white/30 disabled:opacity-50 rounded-full flex items-center justify-center text-white transition-colors border border-white/20">
                            <i class="fas fa-chevron-left text-sm"></i>
                        </button>
                        <div class="text-white text-center bg-black/20 rounded-md px-2 py-1 backdrop-blur-sm">
                            <p class="text-xs opacity-90">Foto <span id="current-photo-index">1</span> / <span id="total-selected-photos">4</span></p>
                        </div>
                        <button onclick="nextPhoto()" id="next-photo-btn"
                                class="w-9 h-9 bg-white/20 hover:bg-white/30 disabled:opacity-50 rounded-full flex items-center justify-center text-white transition-colors border border-white/20">
                            <i class="fas fa-chevron-right text-sm"></i>
                        </button>
                    </div>
                    <div class="flex-1 flex items-center justify-center min-h-0 relative">
                        <div class="w-full h-full relative">
                            <canvas id="photo-preview-canvas"
                                    class="w-full h-full object-contain rounded-md shadow-2xl bg-black/20 border-2 border-white/10"></canvas>
                            <img id="photo-preview-original" class="hidden" alt="Original photo">
                        </div>
                    </div>
                    <div class="mt-2 flex justify-between items-center">
                        <button onclick="backToFrameSelection()"
                                class="bg-gray-600/80 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md transition-colors border border-gray-500 font-medium">
                            <i class="fas fa-arrow-left mr-1.5"></i>
                            Kembali
                        </button>
                        <div class="bg-black/20 backdrop-blur-sm rounded-md px-2 py-1 border border-white/20 text-xs">
                            <span class="opacity-80">Filter:</span>
                            <span id="current-filter-name" class="font-bold text-white ml-1">Tanpa Filter</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter Selection Panel - Right Side --}}
            <div class="w-72 lg:w-80 xl:w-96 flex-shrink-0">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-2 h-full flex flex-col border border-white/20 shadow-xl">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-white font-bold text-base">🎨 Filter Collection</h3>
                        <div class="text-white/60 text-xs">8 pilihan</div>
                    </div>
                    
                    {{-- KEY CHANGE: Added 'grid-rows-4' to distribute space across 4 rows --}}
                    <div class="flex-1 grid grid-cols-2 grid-rows-4 gap-1.5">
                        
                        {{-- All filters now have h-full on the main container to fill the grid cell --}}
                        <div class="filter-option active" data-filter="none" onclick="applyFilter('none')">
                            {{-- KEY CHANGE: Added 'h-full' and 'items-center' to vertically center the content --}}
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-white/30 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-gray-100 to-gray-300 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-image text-gray-600 text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Original</p>
                                        <p class="text-white/70 text-xs leading-tight">Tanpa filter</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="vivid" onclick="applyFilter('vivid')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-orange-400/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-orange-400 to-red-500 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-sun text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Vivid</p>
                                        <p class="text-white/70 text-xs leading-tight">Warna cerah</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="dramatic" onclick="applyFilter('dramatic')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-red-500/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-red-600 to-purple-700 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-fire text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Dramatic</p>
                                        <p class="text-white/70 text-xs leading-tight">Kontras tinggi</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="blackwhite" onclick="applyFilter('blackwhite')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-gray-400/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-gray-700 to-gray-900 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-palette text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">B&W</p>
                                        <p class="text-white/70 text-xs leading-tight">Hitam putih</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="sepia" onclick="applyFilter('sepia')">
                           <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-yellow-500/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-yellow-600 to-orange-700 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-camera-retro text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Sepia</p>
                                        <p class="text-white/70 text-xs leading-tight">Vintage klasik</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="cool" onclick="applyFilter('cool')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-blue-400/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-snowflake text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Cool</p>
                                        <p class="text-white/70 text-xs leading-tight">Tone dingin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="warm" onclick="applyFilter('warm')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-orange-500/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-orange-500 to-red-600 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-fire-alt text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Warm</p>
                                        <p class="text-white/70 text-xs leading-tight">Tone hangat</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="filter-option" data-filter="negative" onclick="applyFilter('negative')">
                            <div class="bg-white/15 hover:bg-white/25 rounded-md p-2 cursor-pointer transition-all border-2 border-transparent hover:border-purple-500/50 h-full flex items-center">
                                <div class="flex items-center space-x-2 w-full">
                                    <div class="w-7 h-7 bg-gradient-to-br from-purple-800 to-pink-900 rounded-md flex-shrink-0 flex items-center justify-center shadow">
                                        <i class="fas fa-adjust text-white text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-white font-semibold">Negative</p>
                                        <p class="text-white/70 text-xs leading-tight">Efek negatif</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Processing Indicator Overlay --}}
    <div id="filter-processing-indicator" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 text-center">
            <i class="fas fa-spinner fa-spin text-white text-4xl mb-4"></i>
            <h3 class="text-white text-xl font-bold mb-2">Memproses Filter</h3>
            <p class="text-white/80">Sedang menerapkan filter ke semua foto...</p>
        </div>
    </div>
</div>