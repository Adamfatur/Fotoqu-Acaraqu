{{-- Dashboard Stats Cards Component --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
    {{-- Sesi Hari Ini --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">📸</span>
        </div>
        <div>
            <p class="text-gray-600 text-sm font-medium mb-1">Sesi Hari Ini</p>
            <p class="text-3xl font-bold text-gray-800" data-stat="today_sessions">{{ number_format($stats['today_sessions'] ?? 0) }}</p>
            <div class="mt-1 text-xs text-gray-600">
                <span class="inline-flex items-center mr-3">
                    <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                    Gratis: <span class="font-semibold ml-1" data-stat="today_free_sessions">{{ number_format($stats['today_free_sessions'] ?? 0) }}</span>
                </span>
                <span class="inline-flex items-center">
                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-1"></span>
                    Berbayar: <span class="font-semibold ml-1" data-stat="today_paid_sessions">{{ number_format($stats['today_paid_sessions'] ?? 0) }}</span>
                </span>
            </div>
            <div class="flex items-center mt-2 text-sm">
                @php 
                    $sessionGrowth = $stats['sessions_growth'] ?? 0;
                    $isPositive = $sessionGrowth >= 0;
                @endphp
                <i class="fas fa-arrow-{{ $isPositive ? 'up' : 'down' }} {{ $isPositive ? 'text-teal-600' : 'text-rose-600' }} mr-1"></i>
                <span class="{{ $isPositive ? 'text-teal-700' : 'text-rose-700' }} font-semibold">{{ $isPositive ? '+' : '' }}{{ number_format($sessionGrowth, 1) }}%</span>
                <span class="text-gray-500 ml-1">dari kemarin</span>
            </div>
        </div>
    </div>

    {{-- Pendapatan Hari Ini --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">💰</span>
        </div>
        <div>
            <p class="text-gray-600 text-sm font-medium mb-1">Pendapatan Hari Ini</p>
            <p class="text-3xl font-bold text-gray-800" data-stat="today_revenue">Rp {{ number_format($stats['today_revenue'] ?? 0, 0, ',', '.') }}</p>
            <div class="flex items-center mt-2 text-sm">
                @php 
                    $revenueGrowth = $stats['revenue_growth'] ?? 0;
                    $isPositive = $revenueGrowth >= 0;
                @endphp
                <i class="fas fa-arrow-{{ $isPositive ? 'up' : 'down' }} {{ $isPositive ? 'text-teal-600' : 'text-rose-600' }} mr-1"></i>
                <span class="{{ $isPositive ? 'text-teal-700' : 'text-rose-700' }} font-semibold">{{ $isPositive ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%</span>
                <span class="text-gray-500 ml-1">dari kemarin</span>
            </div>
        </div>
    </div>

    {{-- Fotobooth Aktif --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">📦</span>
        </div>
        <div>
            <p class="text-gray-600 text-sm font-medium mb-1">Fotobooth Aktif</p>
            <p class="text-3xl font-bold text-gray-800" data-stat="active_boxes">{{ $stats['active_photoboxes'] ?? 0 }}</p>
            <div class="flex items-center mt-2 text-sm">
                <div class="w-2 h-2 bg-teal-500 rounded-full mr-2 animate-pulse"></div>
                <span class="text-gray-600 font-medium">Siap digunakan</span>
            </div>
        </div>
    </div>

    {{-- Sesi Aktif --}}
    <div class="bg-white rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl">🔄</span>
        </div>
        <div>
            <p class="text-gray-600 text-sm font-medium mb-1">Sesi Aktif</p>
            <p class="text-3xl font-bold text-gray-800" data-stat="active_sessions">{{ number_format($stats['active_sessions'] ?? 0) }}</p>
            <div class="flex items-center mt-2 text-sm">
                <div class="w-2 h-2 bg-teal-500 rounded-full mr-2 animate-pulse"></div>
                <span class="text-gray-600 font-medium">Sedang berlangsung</span>
            </div>
        </div>
    </div>
</div>
