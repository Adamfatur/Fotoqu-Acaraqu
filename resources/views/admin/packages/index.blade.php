@extends('admin.layout')

@section('header', 'Manajemen Paket & Harga')
@section('description', 'Kelola paket harga foto dengan berbagai slot frame')

@section('content')
    <style>
        /* Redesign Concept: The "Smart Package Gallery" */
        :root {
            /* Brand palette */
            --brand-teal: #053a63;
            /* Teal Blue */
            --brand-orange: #f29223;
            /* Carrot Orange */
            --brand-curious: #1a90d6;
            /* Curious Blue */
            --brand-dodger: #1fa8f0;
            /* Dodger Blue */

            /* Surfaces and text */
            --card-bg: #ffffff;
            --page-bg: #f7f9fc;
            --border-color: #e9eef5;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;

            /* Backward-compat mapped to brand */
            --navy-color: var(--brand-teal);
            --featured-color: var(--brand-orange);
        }

        .package-card {
            background: var(--card-bg);
            border-radius: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        .featured-ribbon {
            position: absolute;
            top: -1px;
            right: -1px;
            width: 110px;
            height: 110px;
            overflow: hidden;
        }

        .featured-ribbon span {
            position: absolute;
            display: block;
            width: 150px;
            padding: 8px 0;
            background-color: var(--featured-color);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            text-align: center;
            right: -35px;
            top: 25px;
            transform: rotate(45deg);
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-dot.active {
            background-color: var(--brand-teal);
        }

        .status-dot.inactive {
            background-color: #d1d5db;
        }

        .action-dropdown {
            position: relative;
        }

        .action-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            z-index: 10;
            width: 180px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .action-menu a,
        .action-menu button {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: background-color 0.2s;
            text-align: left;
        }

        .action-menu a:hover,
        .action-menu button:hover {
            background-color: rgba(31, 168, 240, 0.06);
            /* brand dodger tint */
        }

        .action-menu .delete-action {
            color: #e74c3c;
        }

        /* Inputs/selects palette focus */
        .select-palette:focus {
            outline: none;
            border-color: var(--brand-curious) !important;
            box-shadow: 0 0 0 3px rgba(26, 144, 214, 0.2);
        }

        /* Price and icon colors using brand palette */
        .price-normal {
            color: var(--brand-curious);
        }

        .price-discount {
            color: var(--brand-orange);
        }

        .icon-check {
            color: var(--brand-teal);
        }

        .icon-hot {
            color: var(--brand-orange);
        }
    </style>

    <style>
        :root {
            --brand-teal: #053a63;
            --brand-orange: #f29223;
            --brand-curious: #1a90d6;
            --brand-dodger: #1fa8f0;
        }

        /* Premium Card Design */
        .package-card {
            background: white;
            border-radius: 1.5rem;
            border: 1px solid #eef2f6;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .package-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: var(--brand-curious);
        }

        /* Gradient Header for Pricing */
        .card-header-gradient {
            background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);
            padding: 1.5rem;
            border-bottom: 1px dashed #e2e8f0;
        }

        .package-card.featured .card-header-gradient {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            /* Orange tint for featured */
        }

        .package-card.free .card-header-gradient {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            /* Green tint for free */
        }

        /* Status Badge */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .status-active {
            background-color: #ecfdf5;
            color: #059669;
        }

        .status-inactive {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        /* Features List */
        .feature-item {
            display: flex;
            align-items: start;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: #4b5563;
            margin-bottom: 0.75rem;
        }

        .feature-icon {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #e0f2fe;
            color: var(--brand-curious);
            margin-top: 0.125rem;
        }

        /* Action Button Overlay */
        .card-actions {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .package-card:hover .card-actions,
        .package-card:focus-within .card-actions,
        .mobile-visible .card-actions {
            opacity: 1;
        }

        .tag-badge {
            background: var(--brand-teal);
            color: white;
            font-size: 0.65rem;
            padding: 2px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            position: absolute;
            top: 1rem;
            left: 1rem;
        }
    </style>

    <div class="space-y-8">
        <!-- Header Section -->
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Daftar Paket</h2>
                <p class="text-gray-500 text-sm mt-1">Kelola variasi paket foto yang tersedia untuk pelanggan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <form id="filterForm" method="GET" action="{{ route('admin.packages.index') }}" class="flex items-center">
                    <div class="relative w-full sm:w-48">
                        <select name="sort" onchange="this.form.submit()"
                            class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none appearance-none cursor-pointer transition-shadow hover:bg-gray-100">
                            <option value="latest" @if(request('sort') == 'latest') selected @endif>🆕 Terbaru</option>
                            <option value="popularity" @if(request('sort') == 'popularity') selected @endif>🔥 Terpopuler
                            </option>
                            <option value="price_asc" @if(request('sort') == 'price_asc') selected @endif>💰 Harga Terendah
                            </option>
                            <option value="price_desc" @if(request('sort') == 'price_desc') selected @endif>💎 Harga Tertinggi
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none text-gray-500">
                            <i class="fas fa-chevron-down text-xs mr-1"></i>
                        </div>
                    </div>
                </form>
                <a href="{{ route('admin.packages.create') }}"
                    class="inline-flex justify-center items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus mr-2"></i> Buat Paket
                </a>
            </div>
        </div>

        <!-- Package Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($packages as $package)
                <div
                    class="package-card {{ $package->is_featured ? 'featured' : '' }} {{ $package->price == 0 ? 'free' : '' }}">

                    {{-- Top Badge --}}
                    @if($package->is_featured)
                        <div
                            class="absolute top-0 right-0 bg-gradient-to-bl from-orange-400 to-orange-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl shadow-sm z-10">
                            <i class="fas fa-star mr-1"></i> UNGGULAN
                        </div>
                    @endif

                    @if($package->price == 0)
                        <div class="tag-badge bg-green-500">GRATIS</div>
                    @elseif($package->print_type == 'none')
                        <div class="tag-badge bg-blue-500">DIGITAL ONLY</div>
                    @else
                        <div class="tag-badge bg-purple-500"><i class="fas fa-print mr-1"></i>CETAK</div>
                    @endif


                    <!-- Card Header (Price Area) -->
                    <div class="card-header-gradient relative group">
                        <div class="flex justify-between items-start pt-6"> <!-- Added sizing for badges -->
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1">{{ $package->name }}</h3>
                                <p class="text-xs text-gray-500 font-medium tracking-wide uppercase">{{ $package->frame_slots }}
                                    Slot Frame</p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-baseline gap-2">
                            @if($package->price == 0)
                                <span class="text-3xl font-extrabold text-green-600">Free</span>
                            @elseif($package->has_discount)
                                <div>
                                    <span
                                        class="text-3xl font-extrabold text-gray-800">{{ $package->formatted_discount_price }}</span>
                                    <span
                                        class="text-sm text-gray-400 line-through font-medium ml-1">{{ $package->formatted_price }}</span>
                                </div>
                            @else
                                <span class="text-3xl font-extrabold text-gray-800">{{ $package->formatted_price }}</span>
                            @endif
                        </div>

                        <!-- Action Menu (Visible on Hover/Click) -->
                        <div class="absolute top-4 right-4 z-20" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-gray-500 hover:text-blue-600 shadow-sm transition-colors border border-gray-100">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                            <div x-show="open" x-transition.origin.top.right
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-30"
                                x-cloak>
                                <a href="{{ route('admin.packages.edit', $package) }}"
                                    class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-pen-to-square w-6"></i> Edit Paket
                                </a>
                                <button onclick="toggleStatus({{ $package->id }})"
                                    class="w-full text-left flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                                    <i class="fas fa-power-off w-6"></i> {{ $package->is_active ? 'Non-aktifkan' : 'Aktifkan' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body (Features) -->
                    <div class="p-6 flex-grow">
                        <div class="space-y-3">
                            @if($package->features && count($package->features) > 0)
                                @foreach(array_slice($package->features, 0, 4) as $feature)
                                    <div class="feature-item">
                                        <span class="feature-icon"><i class="fas fa-check text-[10px]"></i></span>
                                        <span class="leading-tight">{{ $feature }}</span>
                                    </div>
                                @endforeach
                                @if(count($package->features) > 4)
                                    <div class="text-xs text-gray-400 font-medium pl-8 italic">+ {{ count($package->features) - 4 }}
                                        fitur lainnya</div>
                                @endif
                            @else
                                <div class="text-center py-4 text-gray-400 text-sm italic">
                                    Belum ada fitur yang ditambahkan
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer (Stats & Status) -->
                    <div
                        class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center text-xs font-medium">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-2 h-2 rounded-full {{ $package->is_active ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-gray-400' }}"></span>
                            <span class="{{ $package->is_active ? 'text-green-700' : 'text-gray-500' }}">
                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div
                            class="flex items-center gap-1.5 text-gray-500 bg-white px-2 py-1 rounded-md border border-gray-200">
                            <i class="fas fa-fire text-orange-500"></i>
                            <span>{{ $package->photo_sessions_count }} Used</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div
                        class="flex flex-col items-center justify-center p-12 bg-white border-2 border-dashed border-gray-200 rounded-3xl text-center">
                        <div class="w-24 h-24 bg-blue-50 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-box-open text-blue-400 text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Belum Ada Paket</h3>
                        <p class="text-gray-500 max-w-sm mt-2 mb-8">Buat paket pertama Anda untuk mulai menawarkan layanan foto
                            kepada pelanggan.</p>
                        <a href="{{ route('admin.packages.create') }}"
                            class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-blue-500/30 hover:-translate-y-1 transition-all">
                            <i class="fas fa-plus mr-2"></i> Buat Paket Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script>
            // Fungsi ini tidak berubah dan tetap aman digunakan
            function refreshData() {
                location.reload();
            }

            async function toggleStatus(packageId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                try {
                    const response = await axios.post(`/admin/packages/${packageId}/toggle-status`, { _token: csrfToken });
                    if (response.data.success) {
                        location.reload();
                    } else {
                        alert(response.data.message || 'Gagal mengubah status paket.');
                    }
                } catch (error) {
                    console.error("Error toggling status:", error);
                    alert('Terjadi kesalahan saat mencoba mengubah status paket.');
                }
            }
        </script>
    @endpush
@endsection