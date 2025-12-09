{{-- Session Detail Modal Component --}}
<div x-show="modals.sessionDetail" 
     x-cloak
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm"
     @click.self="closeSessionDetailModal()"
     @keydown.escape="closeSessionDetailModal()">
    <div class="bg-white rounded-3xl p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto shadow-2xl"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Detail Sesi Foto</h3>
            <button @click="closeSessionDetailModal()" type="button" class="p-2 text-gray-400 hover:bg-gray-100 rounded-full transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div id="session-detail-content">
            <div class="animate-pulse space-y-4">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
            </div>
        </div>
    </div>
</div>
