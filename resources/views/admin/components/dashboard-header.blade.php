{{-- Dashboard Header Component --}}
<div
    class="bg-gradient-to-r from-slate-100 via-slate-200 to-emerald-100 rounded-3xl p-8 mb-8 shadow-2xl overflow-hidden border-2 border-slate-300">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2 text-slate-800">
                Welcome back, {{ Auth::user()->name }}! 👋
            </h1>
            <p class="text-slate-700 text-base md:text-lg">
                Ringkasan aktivitas Fotobooth hari ini
            </p>
        </div>

        <div class="flex items-center justify-between w-full md:w-auto space-x-6">
            {{-- Refresh Button --}}
            <button @click="refreshAll()"
                class="group px-4 py-2 bg-white/40 hover:bg-white/60 backdrop-blur-sm border border-white/40 text-slate-700 rounded-xl font-medium transition-all transform hover:scale-105 flex items-center space-x-2 shadow-sm hover:shadow-md"
                :disabled="loading.stats" :class="{'opacity-75 cursor-wait': loading.stats}">
                <i class="fas fa-sync-alt" :class="{'animate-spin': loading.stats}"></i>
                <span>Refresh Data</span>
            </button>

            <div class="text-right hidden sm:block">
                <p class="text-slate-800 font-semibold" x-text="currentDate">{{ now()->format('l, d F Y') }}</p>
                <p class="text-slate-700 text-sm"><span x-text="currentTime">{{ now()->format('H:i') }}</span> WIB</p>
            </div>
        </div>
    </div>

</div>