@extends('admin.layout')

@section('header', 'Laporan & Analitik')
@section('description', 'Analisis performa bisnis dan laporan detail')

@section('content')
<style>
    :root {
        --color-navy: #1e3a8a;
        --color-green: #059669;
        --color-bg-main: #f4f7fa;
        --color-bg-sidebar: #ffffff;
        --color-border: #e2e8f0;
        --color-text-header: #1a202c;
        --color-text-body: #4a5568;
        --color-white: #ffffff;
    }

    .reports-container {
        background-color: var(--color-bg-main);
        min-height: calc(100vh - 120px);
        padding: 1rem;
    }

    .filter-sidebar {
        background-color: var(--color-bg-sidebar);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid var(--color-border);
        height: fit-content;
    }

    .main-content {
        background-color: var(--color-bg-sidebar);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02), 0 1px 2px rgba(0,0,0,0.04);
        border: 1px solid var(--color-border);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid var(--color-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .metric-card .label {
        color: var(--color-text-body);
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .metric-card .value {
        color: var(--color-text-header);
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .metric-card .icon-wrapper {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .payment-method-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid var(--color-border);
        transition: all 0.2s ease;
    }

    .payment-method-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .sidebar-section {
        margin-bottom: 1.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--color-border);
    }

    .sidebar-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .sidebar-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--color-text-header);
        margin-bottom: 1rem;
    }

    .tab-nav {
        display: flex;
        background: var(--color-border);
        border-radius: 0.5rem;
        padding: 0.25rem;
        margin-bottom: 1.5rem;
    }

    .tab-button {
        flex: 1;
        padding: 0.75rem 1rem;
        border-radius: 0.25rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: none;
        background: none;
        color: var(--color-text-body);
        cursor: pointer;
    }

    .tab-button.active {
        background: white;
        color: var(--color-text-header);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .data-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
        border: 1px solid var(--color-border);
        transition: background-color 0.2s;
    }

    .data-list-item:hover {
        background-color: #f8fafc;
    }

    .tab-panel {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="reports-container">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Sidebar Filter -->
        <div class="lg:col-span-1">
            <div class="filter-sidebar">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">Periode Laporan</h3>
                    <form action="{{ route('admin.reports.index') }}" method="GET" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Metode</option>
                                <option value="free" {{ $paymentMethod === 'free' ? 'selected' : '' }}>Free</option>
                                <option value="qris" {{ $paymentMethod === 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="edc" {{ $paymentMethod === 'edc' ? 'selected' : '' }}>EDC/Kartu</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all shadow-sm flex items-center justify-center">
                            <i class="fas fa-filter mr-2"></i>Terapkan Filter
                        </button>
                    </form>
                </div>

                <div class="sidebar-section">
                    <h3 class="sidebar-title">Export Laporan</h3>
                    <div class="space-y-3">
                        <button onclick="exportReport('sessions', 'csv')" class="w-full text-left flex items-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-file-csv text-xl text-blue-500"></i>
                            <span class="ml-3 font-medium">Data Sesi (CSV)</span>
                        </button>
                        <button onclick="exportReport('revenue', 'csv')" class="w-full text-left flex items-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-chart-bar text-xl text-green-500"></i>
                            <span class="ml-3 font-medium">Laporan Revenue (CSV)</span>
                        </button>
                        <p class="text-xs text-gray-500">Export mengikuti filter periode & metode pembayaran.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="main-content">
                
                <!-- Metrics Cards -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div>
                            <p class="label">Total Sesi</p>
                            <p class="value">{{ number_format($totalSessions) }}</p>
                        </div>
                        <div class="icon-wrapper" style="background-color: #1e3a8a;">
                            <i class="fas fa-camera"></i>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div>
                            <p class="label">Sesi Selesai</p>
                            <p class="value">{{ number_format($completedSessions) }}</p>
                        </div>
                        <div class="icon-wrapper" style="background-color: #059669;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div>
                            <p class="label">Total Revenue</p>
                            <p class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        </div>
                        <div class="icon-wrapper" style="background-color: #1e3a8a;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div>
                            <p class="label">Rata-rata Order</p>
                            <p class="value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</p>
                        </div>
                        <div class="icon-wrapper" style="background-color: #059669;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Breakdown -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Breakdown Metode Pembayaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($paymentMethodStats as $stat)
                            <div class="payment-method-card">
                                <div class="flex items-center space-x-3">
                                    @if($stat->method === 'free')
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-gift text-green-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">Free Sessions</p>
                                            <p class="text-sm text-gray-600">{{ $stat->count }} sesi</p>
                                        </div>
                                    @elseif($stat->method === 'qris')
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-qrcode text-blue-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">QRIS</p>
                                            <p class="text-sm text-gray-600">{{ $stat->count }} sesi</p>
                                        </div>
                                    @elseif($stat->method === 'edc')
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-credit-card text-purple-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">EDC/Kartu</p>
                                            <p class="text-sm text-gray-600">{{ $stat->count }} sesi</p>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-money-bill text-gray-600 text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800">{{ ucfirst($stat->method) }}</p>
                                            <p class="text-sm text-gray-600">{{ $stat->count }} sesi</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-3 text-right">
                                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($stat->revenue, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">
                                <i class="fas fa-chart-pie text-3xl mb-2"></i>
                                <p class="text-sm">Tidak ada data pembayaran untuk periode ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Tabs Content -->
                <div x-data="{ activeTab: 'visualisasi' }">
                    <nav class="tab-nav">
                        <button @click="activeTab = 'visualisasi'" :class="{ 'active': activeTab === 'visualisasi' }" class="tab-button">Visualisasi</button>
                        <button @click="activeTab = 'data'" :class="{ 'active': activeTab === 'data' }" class="tab-button">Data Rinci</button>
                    </nav>
                    
                    <!-- Tab: Visualisasi -->
                    <div x-show="activeTab === 'visualisasi'" class="tab-panel">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sesi Harian</h3>
                                <div class="h-80"><canvas id="dailySessionsChart"></canvas></div>
                            </div>
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue per Paket</h3>
                                <div class="h-80"><canvas id="packageRevenueChart"></canvas></div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Data Rinci -->
                    <div x-show="activeTab === 'data'" class="tab-panel" style="display: none;">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- Photobox Performance -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Performa Photobox</h3>
                                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">Urut: terbanyak</span>
                                </div>
                                <div class="space-y-3">
                                    @forelse($photoboxPerformance as $photobox)
                                    <div class="data-list-item">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $photobox->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $photobox->code }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">{{ $photobox->sessions }} sesi</p>
                                            <p class="text-sm text-green-600 font-medium">Rp {{ number_format($photobox->revenue, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-cube text-3xl mb-2"></i>
                                        <p>Tidak ada data photobox untuk periode ini</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Top Customers -->
                            <div class="bg-white p-6 rounded-lg border border-gray-200">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-800">Top Customers</h3>
                                    <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">Top 5</span>
                                </div>
                                <div class="space-y-3">
                                    @forelse($topCustomers as $customer)
                                    <div class="data-list-item">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $customer->customer_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $customer->customer_email }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">{{ $customer->sessions }} sesi</p>
                                            <p class="text-sm text-green-600 font-medium">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-user text-3xl mb-2"></i>
                                        <p>Tidak ada data customer untuk periode ini</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Recent Sessions Table -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">Detail Sesi Terbaru</h3>
                                <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-600">10 terbaru</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photobox</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($recentSessions ?? [] as $session)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #{{ $session->id }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <div>
                                                        <div class="font-medium">{{ $session->customer_name }}</div>
                                                        <div class="text-gray-500">{{ $session->customer_email }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $session->package->name ?? 'Tidak ada paket' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $session->photobox->code ?? 'N/A' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusClasses = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'active' => 'bg-blue-100 text-blue-800',
                                                            'photo_selection' => 'bg-purple-100 text-purple-800',
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'cancelled' => 'bg-red-100 text-red-800'
                                                        ];
                                                        $statusClass = $statusClasses[$session->session_status] ?? 'bg-gray-100 text-gray-800';
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                        {{ ucfirst($session->session_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @php
                                                        $paymentLog = $session->paymentLogs->first();
                                                        $paymentMethod = $paymentLog ? $paymentLog->payment_method : 'N/A';
                                                    @endphp
                                                    
                                                    @if($paymentMethod === 'free')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-gift mr-1"></i>Free
                                                        </span>
                                                    @elseif($paymentMethod === 'qris')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-qrcode mr-1"></i>QRIS
                                                        </span>
                                                    @elseif($paymentMethod === 'edc')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                            <i class="fas fa-credit-card mr-1"></i>EDC
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ $paymentMethod }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    Rp {{ number_format($session->total_price, 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $session->created_at->format('d/m/Y H:i') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                                    <i class="fas fa-camera text-3xl mb-2"></i>
                                                    <p>Tidak ada data sesi untuk periode ini</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Export function
function exportReport(type, format) {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    const paymentMethod = document.querySelector('select[name="payment_method"]').value;
    
    let url = `/admin/reports/export?type=${type}&format=${format}&start_date=${startDate}&end_date=${endDate}`;
    if (paymentMethod) {
        url += `&payment_method=${paymentMethod}`;
    }
    
    window.open(url, '_blank');
}

// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Chart data from Laravel
    const dailySessionsData = @json($dailySessions);
    const packageRevenueData = @json($packageRevenue);

    // Daily Sessions Chart
    const dailyCtx = document.getElementById('dailySessionsChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailySessionsData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
                }),
                datasets: [{
                    label: 'Sesi Harian',
                    data: dailySessionsData.map(item => item.count),
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30, 58, 138, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Package Revenue Chart
    const packageCtx = document.getElementById('packageRevenueChart');
    if (packageCtx) {
        new Chart(packageCtx, {
            type: 'doughnut',
            data: {
                labels: packageRevenueData.map(item => item.name),
                datasets: [{
                    data: packageRevenueData.map(item => item.revenue),
                    backgroundColor: [
                        '#1e3a8a',
                        '#059669',
                        '#7c3aed',
                        '#dc2626',
                        '#ea580c'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + context.parsed.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

@endsection
