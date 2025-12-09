{{-- Dashboard JavaScript Component --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function dashboardData() {
        // Private chart instances (not reactive)
        let revenueChartInstance = null;
        let statusChartInstance = null;

        return {
            // State
            loading: { sessions: false, photoboxes: false, stats: false },
            currentTime: '',
            currentDate: '',
            modals: { sessionDetail: false, confirm: false, alert: false },
            confirm: { title: '', message: '', confirmText: 'Ya', cancelText: 'Batal', variant: 'warning', _resolver: null },
            alert: { title: '', message: '', okText: 'Tutup', variant: 'info' },


            // Data
            stats: @json($stats ?? []),
            photoboxes: @json($photoboxes ?? []),
            chartSummary: {
                avgDaily: {{ $stats['avg_daily_revenue'] ?? 0 }},
                highest: {{ $stats['highest_daily_revenue'] ?? 0 }},
                growth: {{ number_format($stats['revenue_growth'] ?? 0, 1) }}
        },
            // revenueChartInstance removed from reactive state
            // statusChartInstance removed from reactive state
            currentTime: '',
            currentDate: '',

            // Lifecycle
            init() {
                this.setupCharts();
                this.startRealTimeUpdates();
                this.initClock();
            },

            initClock() {
                const updateTime = () => {
                    const now = new Date();
                    const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    // Indonesian locale for date
                    this.currentDate = now.toLocaleDateString('id-ID', optionsDate);
                    // 24-hour format logic
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    this.currentTime = `${hours}:${minutes}`;
                };
                updateTime();
                setInterval(updateTime, 1000);
            },

            async refreshAll() {
                this.loading.stats = true;
                this.loading.sessions = true;
                this.loading.photoboxes = true;

                try {
                    await Promise.all([
                        this.updateStats(),
                        this.refreshSessions(),
                        this.refreshPhotoboxes(),
                        this.updateActivityFeed()
                    ]);
                    this.showNotification('Dashboard berhasil diperbarui', 'success');
                } catch (error) {
                    this.showNotification('Gagal memperbarui dashboard', 'error');
                } finally {
                    this.loading.stats = false;
                    this.loading.sessions = false;
                    this.loading.photoboxes = false;
                }
            },


            // Real-time Updates
            startRealTimeUpdates() {
                setInterval(() => this.updateStats(), 30000);
                setInterval(() => this.updateActivityFeed(), 15000);
            },

            async updateStats() {
                if (this.loading.stats) return;
                this.loading.stats = true;
                try {
                    const response = await axios.get('/admin/dashboard/stats');
                    this.stats = { ...this.stats, ...response.data.today };
                    this.chartSummary = { ...this.chartSummary, ...response.data.charts };
                    document.querySelectorAll('[data-stat]').forEach(el => {
                        const key = el.dataset.stat;
                        if (this.stats[key] !== undefined) {
                            const value = this.stats[key];
                            // Format revenue with IDR style, others as plain number
                            if (key.includes('revenue')) {
                                const num = Number(value) || 0;
                                el.textContent = `Rp ${num.toLocaleString('id-ID')}`;
                            } else {
                                const num = Number(value) || 0;
                                el.textContent = num.toLocaleString('id-ID');
                            }
                        }
                    });
                } catch (error) {
                    console.error('Failed to update stats:', error);
                } finally {
                    this.loading.stats = false;
                }
            },

            async updateActivityFeed() {
                try {
                    const response = await axios.get('/admin/dashboard/activities');
                    const activityFeed = document.getElementById('activity-feed');
                    if (activityFeed) activityFeed.innerHTML = response.data.html;
                } catch (error) {
                    console.error('Failed to update activity feed:', error);
                }
            },

            // UI Actions
            async refreshSessions() {
                this.loading.sessions = true;
                try {
                    const response = await axios.get('/admin/dashboard/recent-sessions');
                    document.getElementById('recent-sessions').innerHTML = response.data.html;
                    this.showNotification('Sesi terbaru berhasil dimuat.', 'success');
                } catch (error) {
                    this.showNotification('Gagal memuat sesi terbaru.', 'error');
                } finally {
                    this.loading.sessions = false;
                }
            },

            async refreshPhotoboxes() {
                this.loading.photoboxes = true;
                try {
                    const response = await axios.get('/admin/dashboard/photoboxes');
                    document.getElementById('photobox-grid').innerHTML = response.data.html;
                    this.showNotification('Status photobox berhasil diperbarui.', 'success');
                } catch (error) {
                    this.showNotification('Gagal memperbarui status photobox.', 'error');
                } finally {
                    this.loading.photoboxes = false;
                }
            },

            // Modal Controls
            closeSessionDetailModal() {
                this.modals.sessionDetail = false;
                const content = document.getElementById('session-detail-content');
                if (content) {
                    content.innerHTML = `<div class="animate-pulse space-y-4"><div class="h-4 bg-gray-200 rounded w-3/4"></div><div class="h-4 bg-gray-200 rounded w-1/2"></div><div class="h-4 bg-gray-200 rounded w-5/6"></div></div>`;
                }
            },

            async showSessionDetail(sessionId) {
                this.modals.sessionDetail = true;
                try {
                    const response = await axios.get(`/admin/sessions/${sessionId}/detail`);
                    document.getElementById('session-detail-content').innerHTML = response.data.html;
                } catch (error) {
                    document.getElementById('session-detail-content').innerHTML = `<div class="text-center py-8"><i class="fas fa-exclamation-triangle text-rose-400 text-3xl mb-3"></i><p class="text-gray-600">Gagal memuat detail sesi.</p></div>`;
                }
            },

            // Actions
            async handleAction(url, method, confirmMessage, successMessage, errorMessage, variant = 'warning') {
                if (confirmMessage) {
                    const ok = await this.openConfirm({ title: 'Konfirmasi', message: confirmMessage, variant });
                    if (!ok) return;
                }

                this.showNotification('Memproses permintaan...', 'info');
                try {
                    const response = await axios({ method, url, data: { admin_id: {{ Auth::id() }} } });
                    if (response.data.success) {
                        this.openAlert({ title: 'Berhasil', message: successMessage || response.data.message, variant: 'success' });
                        await this.refreshPhotoboxes();
                        await this.updateStats();
                    } else {
                        throw new Error(response.data.error || 'Terjadi kesalahan');
                    }
                } catch (error) {
                    const msg = error.response?.data?.error || error.message;
                    this.openAlert({ title: 'Gagal', message: `${errorMessage}: ${msg}`, variant: 'error' });
                }
            },

            forceStop(photoboxId) {
                this.handleAction(`/admin/photoboxes/${photoboxId}/force-stop`, 'post', 'Hentikan sesi secara paksa? Semua foto akan hilang!', 'Sesi berhasil dihentikan.', 'Gagal menghentikan sesi', 'danger');
            },

            startTestSession(photoboxId) {
                this.handleAction(`/admin/photoboxes/${photoboxId}/test`, 'post', 'Mulai sesi test untuk photobox ini?', 'Sesi test berhasil dimulai.', 'Gagal memulai sesi test');
            },



            showPhotoboxDetail(photoboxId) {
                console.log('Show photobox detail:', photoboxId);
            },

            // Removed global stop-all action by request



            async updateChart(period) {
                if (!revenueChartInstance) return;

                // Show loading state for chart
                const chartCanvas = document.getElementById('revenueChart');
                if (chartCanvas) chartCanvas.style.opacity = '0.5';

                try {
                    const response = await axios.get('/admin/dashboard/revenue-chart', { params: { period } });

                    // Update Summary Cards
                    this.chartSummary = response.data.summary;

                    // Update Chart Data
                    // Extract labels and data from response
                    const labels = response.data.chart.map(item => item.date);
                    const revenues = response.data.chart.map(item => item.revenue);

                    revenueChartInstance.data.labels = labels;
                    revenueChartInstance.data.datasets[0].data = revenues;
                    revenueChartInstance.update();

                } catch (error) {
                    console.error('Failed to update chart:', error);
                    // this.showNotification('Gagal memperbarui grafik: ' + (error.response?.data?.message || error.message), 'error');
                } finally {
                    if (chartCanvas) chartCanvas.style.opacity = '1';
                }
            },



            // Notification System
            showNotification(message, type = 'info') {
                const container = document.body;
                const notification = document.createElement('div');
                const icons = { success: 'fa-check-circle', error: 'fa-exclamation-triangle', info: 'fa-info-circle' };
                const colors = { success: 'bg-teal-600', error: 'bg-rose-600', info: 'bg-indigo-600' };

                notification.className = `fixed top-5 right-5 p-4 rounded-xl shadow-lg z-50 transition-all duration-300 max-w-md text-gray-100 ${colors[type]}`;
                notification.innerHTML = `<div class="flex items-center space-x-3"><i class="fas ${icons[type]} text-lg"></i><span class="font-medium">${message}</span></div>`;

                notification.style.transform = 'translateX(110%)';
                container.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 10);

                setTimeout(() => {
                    notification.style.transform = 'translateX(110%)';
                    setTimeout(() => notification.remove(), 300);
                }, 4000);
            },

            // Confirm/Alert helpers
            openConfirm({ title, message, confirmText = 'Ya', cancelText = 'Batal', variant = 'warning' }) {
                return new Promise((resolve) => {
                    this.confirm = { title, message, confirmText, cancelText, variant, _resolver: resolve };
                    this.modals.confirm = true;
                });
            },
            closeConfirm(confirmed) {
                if (this.confirm._resolver) this.confirm._resolver(!!confirmed);
                this.modals.confirm = false;
                this.confirm = { title: '', message: '', confirmText: 'Ya', cancelText: 'Batal', variant: 'warning', _resolver: null };
            },
            openAlert({ title, message, okText = 'Tutup', variant = 'info' }) {
                this.alert = { title, message, okText, variant };
                this.modals.alert = true;
            },
            closeAlert() {
                this.modals.alert = false;
                this.alert = { title: '', message: '', okText: 'Tutup', variant: 'info' };
            },

            // Chart Setup
            setupCharts() {
                this.initRevenueChart();
                this.initStatusChart();
            },

            initRevenueChart() {
                const ctx = document.getElementById('revenueChart')?.getContext('2d');
                if (!ctx) return;

                revenueChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(collect($revenueChart ?? [])->pluck('date')),
                        datasets: [{
                            label: 'Revenue (Rp)',
                            data: @json(collect($revenueChart ?? [])->pluck('revenue')),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#14b8a6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { display: false }, ticks: { color: '#6b7280' } },
                            x: { grid: { display: false }, ticks: { color: '#6b7280' } }
                        }
                    }
                });
            },

            initStatusChart() {
                const ctx = document.getElementById('statusChart')?.getContext('2d');
                if (!ctx) return;

                const statusData = @json($sessionStatusChart ?? []);

                statusChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(statusData).map(key => key.charAt(0).toUpperCase() + key.slice(1).replace('_', ' ')),
                        datasets: [{
                            data: Object.values(statusData),
                            backgroundColor: ['#f59e0b', '#3b82f6', '#14b8a6', '#ef4444', '#6b7280'],
                            borderWidth: 4,
                            borderColor: '#ffffff',
                            hoverOffset: 15,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true, pointStyle: 'circle', font: { size: 12 } } }
                        },
                        cutout: '65%'
                    }
                });
            },



            // ...existing methods...
        };
    }

    // Global functions for AJAX-loaded content
    window.showSessionDetail = function (sessionId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.showSessionDetail) {
            dashboard.showSessionDetail(sessionId);
        }
    }

    window.showPhotoboxDetail = function (photoboxId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.showPhotoboxDetail) {
            dashboard.showPhotoboxDetail(photoboxId);
        }
    }

    window.startTestSession = function (photoboxId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.startTestSession) {
            dashboard.startTestSession(photoboxId);
        }
    }

    window.forceStop = function (photoboxId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.forceStop) {
            dashboard.forceStop(photoboxId);
        }
    }





    window.resetPhotobox = function (photoboxId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.resetPhotobox) {
            dashboard.resetPhotobox(photoboxId);
        }
    }

    window.viewSessionDetail = function (sessionId) {
        const dashboard = Alpine.$data(document.querySelector('[x-data*="dashboardData"]'));
        if (dashboard && dashboard.viewSessionDetail) {
            dashboard.viewSessionDetail(sessionId);
        }
    }
</script>