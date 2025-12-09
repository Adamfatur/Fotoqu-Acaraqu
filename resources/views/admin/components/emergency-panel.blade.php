{{-- Emergency Panel Component --}}
<div x-show="showEmergencyPanel" 
     x-transition:enter="transition ease-out duration-300" 
     x-transition:enter-start="opacity-0 transform scale-95" 
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200" 
     x-transition:leave-start="opacity-100 transform scale-100" 
     x-transition:leave-end="opacity-0 transform scale-95"
     class="mt-6 p-6 bg-white/90 backdrop-blur-sm border border-slate-300 rounded-2xl shadow-lg">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-red-800 flex items-center">
            <i class="fas fa-shield-alt mr-2"></i>
            Emergency Control Panel
        </h3>
        <button @click="showEmergencyPanel = false" class="text-red-600 hover:text-red-800 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-4 rounded-xl border border-orange-200">
            <h4 class="font-bold text-orange-800 mb-2">System Reset</h4>
            <p class="text-sm text-orange-600 mb-3">Reset semua Fotobooth ke kondisi standby.</p>
            <button @click="systemReset()" 
                    class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-orange-50 rounded-xl font-medium transition-colors">
                <i class="fas fa-sync-alt mr-2"></i>Reset Semua Fotobooth
            </button>
        </div>
    </div>
</div>
