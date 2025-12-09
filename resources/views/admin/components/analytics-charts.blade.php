{{-- Revenue Analytics & Status Chart Component --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 mb-8">
    {{-- Revenue Analytics --}}
    <div class="lg:col-span-2 bg-white rounded-3xl p-5 sm:p-8 shadow-lg border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Revenue Analytics</h3>
                <p class="text-sm sm:text-base text-gray-600">Tren pendapatan & performa harian</p>
            </div>
            <div class="flex items-center justify-between sm:justify-end space-x-4 w-full sm:w-auto">
                <div class="flex bg-gray-100 rounded-xl p-1 overflow-x-auto" x-data="{ active: 'week' }">
                    <button @click="active = 'week'; updateChart('week')"
                        :class="active === 'week' ? 'bg-white shadow-sm text-indigo-700' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm rounded-lg transition-all font-medium whitespace-nowrap">7D</button>
                    <button @click="active = 'month'; updateChart('month')"
                        :class="active === 'month' ? 'bg-white shadow-sm text-indigo-700' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm rounded-lg transition-all font-medium whitespace-nowrap">30D</button>
                    <button @click="active = '90days'; updateChart('90days')"
                        :class="active === '90days' ? 'bg-white shadow-sm text-indigo-700' : 'text-gray-600'"
                        class="px-3 sm:px-4 py-2 text-xs sm:text-sm rounded-lg transition-all font-medium whitespace-nowrap">90D</button>
                </div>
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-slate-700 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-chart-line text-indigo-100 text-sm sm:text-base"></i>
                </div>
            </div>
        </div>
        <div class="relative h-64 sm:h-80">
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mt-6 pt-6 border-t border-gray-100">
            <div
                class="flex flex-row sm:flex-col items-center justify-between sm:justify-center p-3 sm:p-0 bg-gray-50 sm:bg-transparent rounded-xl sm:rounded-none">
                <div class="text-left sm:text-center">
                    <span class="text-sm text-gray-500 font-medium sm:hidden">Rata-rata Harian</span>
                </div>
                <div class="text-right sm:text-center">
                    <div class="text-lg sm:text-2xl font-bold text-gray-800"
                        x-text="'Rp ' + chartSummary.avgDaily.toLocaleString('id-ID')">
                        {{ 'Rp ' . number_format($stats['avg_daily_revenue'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="hidden sm:block text-sm text-gray-500 font-medium">Rata-rata Harian</div>
                </div>
            </div>
            <div
                class="flex flex-row sm:flex-col items-center justify-between sm:justify-center p-3 sm:p-0 bg-gray-50 sm:bg-transparent rounded-xl sm:rounded-none">
                <div class="text-left sm:text-center">
                    <span class="text-sm text-gray-500 font-medium sm:hidden">Tertinggi</span>
                </div>
                <div class="text-right sm:text-center">
                    <div class="text-lg sm:text-2xl font-bold text-teal-600"
                        x-text="'Rp ' + chartSummary.highest.toLocaleString('id-ID')">
                        {{ 'Rp ' . number_format($stats['highest_daily_revenue'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="hidden sm:block text-sm text-gray-500 font-medium">Tertinggi</div>
                </div>
            </div>
            <div
                class="flex flex-row sm:flex-col items-center justify-between sm:justify-center p-3 sm:p-0 bg-gray-50 sm:bg-transparent rounded-xl sm:rounded-none">
                <div class="text-left sm:text-center">
                    <span class="text-sm text-gray-500 font-medium sm:hidden">Pertumbuhan</span>
                </div>
                <div class="text-right sm:text-center">
                    <div class="text-lg sm:text-2xl font-bold text-indigo-700" x-text="chartSummary.growth + '%'">
                        +{{ number_format($stats['revenue_growth'] ?? 0, 1) }}%</div>
                    <div class="hidden sm:block text-sm text-gray-500 font-medium">Pertumbuhan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Chart --}}
    <div class="bg-white rounded-3xl p-5 sm:p-8 shadow-lg border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-800">Status Sesi</h3>
                <p class="text-sm sm:text-base text-gray-600">Distribusi status hari ini</p>
            </div>
            <div
                class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-teal-500 to-indigo-700 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chart-pie text-indigo-100 text-sm sm:text-base"></i>
            </div>
        </div>
        <div class="relative h-60">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>