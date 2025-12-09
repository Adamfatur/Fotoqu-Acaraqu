{{-- Reusable Confirmation and Alert Modals (Alpine-driven) --}}
<div>
    {{-- Confirm Modal --}}
    <div x-show="modals.confirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeConfirm(false)"></div>
        <div class="relative bg-white w-full max-w-md mx-4 rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                         :class="confirm.variant === 'danger' ? 'bg-rose-100 text-rose-600' : (confirm.variant === 'warning' ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600')">
                        <i class="fas" :class="confirm.variant === 'danger' ? 'fa-triangle-exclamation' : (confirm.variant === 'warning' ? 'fa-circle-exclamation' : 'fa-question-circle')"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-800" x-text="confirm.title || 'Konfirmasi'"></h3>
                        <p class="mt-1 text-gray-600" x-text="confirm.message"></p>
                    </div>
                    <button class="p-2 text-gray-400 hover:text-gray-600" @click="closeConfirm(false)"><i class="fas fa-times"></i></button>
                </div>
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <button class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50"
                            @click="closeConfirm(false)" x-text="confirm.cancelText || 'Batal'"></button>
                    <button class="px-4 py-2 rounded-xl text-white"
                            :class="confirm.variant === 'danger' ? 'bg-rose-600 hover:bg-rose-700' : (confirm.variant === 'warning' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-indigo-600 hover:bg-indigo-700')"
                            @click="closeConfirm(true)"
                            x-text="confirm.confirmText || 'Ya'"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Modal --}}
    <div x-show="modals.alert" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeAlert()"></div>
        <div class="relative bg-white w-full max-w-md mx-4 rounded-2xl shadow-2xl overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                         :class="alert.variant === 'success' ? 'bg-teal-100 text-teal-600' : (alert.variant === 'error' ? 'bg-rose-100 text-rose-600' : 'bg-indigo-100 text-indigo-600')">
                        <i class="fas" :class="alert.variant === 'success' ? 'fa-check-circle' : (alert.variant === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle')"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-800" x-text="alert.title || 'Informasi'"></h3>
                        <p class="mt-1 text-gray-600" x-text="alert.message"></p>
                    </div>
                    <button class="p-2 text-gray-400 hover:text-gray-600" @click="closeAlert()"><i class="fas fa-times"></i></button>
                </div>
                <div class="mt-6 flex items-center justify-end">
                    <button class="px-4 py-2 rounded-xl text-white bg-indigo-600 hover:bg-indigo-700" @click="closeAlert()" x-text="alert.okText || 'Tutup'"></button>
                </div>
            </div>
        </div>
    </div>
</div>
