@extends('admin.layout')

@section('header', 'Edit Paket')
@section('description', 'Ubah informasi paket frame dan harga')

@section('content')
    <style>
        /* Menggunakan kembali style yang konsisten dari halaman Tambah Paket */
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
            --form-bg: #ffffff;
            --page-bg: #f7f9fc;
            --border-color: #e9eef5;
            --text-primary: #2c3e50;
            --text-secondary: #7f8c8d;
            --input-bg: #fdfdfe;
            --danger-color: #e74c3c;

            /* Backward-compat mapped to brand */
            --navy-color: var(--brand-teal);
            --green-color: var(--brand-curious);
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--input-bg);
            border: 1px solid #dce4f2;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--brand-curious);
            box-shadow: 0 0 0 3px rgba(26, 144, 214, 0.2);
        }

        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }

        .preview-sticky-container {
            position: sticky;
            top: 2rem;
        }

        .preview-card,
        .stats-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px -5px rgba(30, 58, 138, 0.05);
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body {
            padding: 1.5rem;
        }

        .live-package-preview {
            background: #ffffff;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e3e8f1;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.05);
            text-align: center;
        }

        .danger-zone {
            border: 2px solid #fee2e2;
            background-color: #fef2f2;
            border-radius: 1rem;
            padding: 1.5rem;
        }
    </style>

    <div class="max-w-7xl mx-auto" x-data="packageForm()">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.packages.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 rounded-lg transition-all duration-200 border border-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
                    <form action="{{ route('admin.packages.update', $package) }}" method="POST"
                        class="p-6 sm:p-8 space-y-10">
                        @csrf
                        @method('PUT')

                        <section>
                            <h2 class="form-section-title">Informasi Dasar</h2>
                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Paket <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" x-model="name"
                                        class="form-input @error('name') border-red-500 @enderror">
                                    @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="frame_slots" class="block text-sm font-medium text-gray-700 mb-2">Jumlah
                                        Slot Foto <span class="text-red-500">*</span></label>

                                    <div x-show="printType === 'strip'" class="relative">
                                        <input type="text" value="6 Slot (Standard Strip)" readonly
                                            class="form-input bg-gray-100 text-gray-500 cursor-not-allowed">
                                        <input type="hidden" name="frame_slots" value="6">
                                        <p class="text-sm text-gray-500 mt-1">Format strip standar menggunakan 6 slot (3
                                            foto + 3 duplikat).</p>
                                    </div>

                                    <div x-show="printType !== 'strip'">
                                        <input type="number" id="frame_slots_custom" name="frame_slots" x-model="slots"
                                            min="1" class="form-input @error('frame_slots') border-red-500 @enderror"
                                            placeholder="Contoh: 1, 3, 4">
                                        <p class="text-sm text-gray-500 mt-1">Masukkan jumlah foto yang akan diambil dalam
                                            satu sesi.</p>
                                    </div>

                                    @error('frame_slots')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="print_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Cetak
                                        <span class="text-red-500">*</span></label>
                                    <select id="print_type" name="print_type" x-model="printType"
                                        @change="if(printType === 'strip') { slots = 6; printCount = 1; } else if(printType === 'none') { printCount = 0; } else { printCount = 1; }"
                                        class="form-input @error('print_type') border-red-500 @enderror">
                                        <option value="strip">Print 1 4R (2 Strip)</option>
                                        <option value="custom">Print Custom 4R</option>
                                        <option value="none">Digital Only (Tanpa Cetak)</option>
                                    </select>
                                    <p class="text-sm text-gray-500 mt-1" x-show="printType === 'strip'">Mencetak 1 lembar
                                        4R yang berisi 2 strip foto.</p>
                                    <p class="text-sm text-gray-500 mt-1" x-show="printType === 'custom'">Mencetak lembaran
                                        4R sesuai jumlah yang ditentukan.</p>
                                    <p class="text-sm text-gray-500 mt-1" x-show="printType === 'none'">Tidak ada hasil
                                        cetak, hanya file digital.</p>
                                    @error('print_type')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div x-show="printType !== 'none'">
                                    <label for="print_count" class="block text-sm font-medium text-gray-700 mb-2">Jumlah
                                        Cetak (Lembar) <span class="text-red-500">*</span></label>
                                    <input type="number" id="print_count" name="print_count" x-model="printCount" min="0"
                                        class="form-input @error('print_count') border-red-500 @enderror">
                                    <p class="text-sm text-gray-500 mt-1">Jumlah lembar foto yang akan dicetak per sesi.</p>
                                    @error('print_count')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                </div>

                                <!-- Hidden input to ensure 0 is sent if printType is 'none' -->
                                <input type="hidden" name="print_count" :value="0" x-if="printType === 'none'">

                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                    <textarea id="description" name="description" x-model="description" rows="3"
                                        class="form-input"></textarea>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h2 class="form-section-title">💰 Informasi Harga</h2>
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">

                                <!-- Free Package Toggle -->
                                <div
                                    class="flex items-center mb-6 bg-white p-4 rounded-xl border border-blue-200 shadow-sm">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" id="is_free" x-model="isFree" @change="toggleFree()"
                                            class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500 transition duration-150 ease-in-out">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_free" class="font-bold text-gray-700">Set sebagai Paket
                                            Gratis</label>
                                        <p class="text-gray-500 text-xs">Aktifkan opsi ini untuk membuat paket tanpa biaya
                                            (Rp 0).</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6"
                                    :class="{'opacity-50 grayscale pointer-events-none select-none': isFree}">
                                    <!-- Harga Dasar -->
                                    <div class="space-y-3">
                                        <label for="price"
                                            class="flex items-center text-sm font-semibold text-gray-800 mb-2">
                                            <i class="fas fa-tag text-blue-500 mr-2"></i>
                                            Harga Dasar <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                                <span class="text-gray-600 font-medium">Rp</span>
                                            </div>
                                            <input type="number" id="price" name="price" x-model.number="price" min="0"
                                                step="1000"
                                                class="w-full pl-12 pr-4 py-3 text-lg font-semibold border-2 border-blue-200 rounded-lg focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-200 bg-white shadow-sm @error('price') border-red-500 ring-red-100 @enderror">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                                <i class="fas fa-coins text-blue-400"></i>
                                            </div>
                                        </div>
                                        @error('price')<p class="text-red-500 text-sm mt-1 flex items-center"><i
                                        class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>@enderror
                                        <p class="text-xs text-blue-600 flex items-center">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Harga yang akan dibayar customer
                                        </p>
                                    </div>

                                    <!-- Harga Diskon -->
                                    <div class="space-y-3">
                                        <label for="discount_price"
                                            class="flex items-center text-sm font-semibold text-gray-800 mb-2">
                                            <i class="fas fa-percentage text-green-500 mr-2"></i>
                                            Harga Diskon
                                            <span
                                                class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Opsional</span>
                                        </label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                                <span class="text-gray-600 font-medium">Rp</span>
                                            </div>
                                            <input type="number" id="discount_price" name="discount_price"
                                                x-model.number="discount" min="0" step="1000"
                                                class="w-full pl-12 pr-4 py-3 text-lg font-semibold border-2 border-green-200 rounded-lg focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-200 bg-white shadow-sm">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                                <i class="fas fa-fire text-green-400"></i>
                                            </div>
                                        </div>
                                        <p class="text-xs text-green-600 flex items-center">
                                            <i class="fas fa-star mr-1"></i>
                                            Harga promo untuk menarik customer
                                        </p>
                                    </div>
                                </div>

                                <!-- Price Comparison Preview -->
                                <div class="mt-6 p-4 bg-white rounded-lg border border-gray-200 shadow-sm"
                                    x-show="price > 0">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-calculator text-purple-500 mr-2"></i>
                                        Preview Harga
                                    </h4>
                                    <div class="flex items-center gap-4">
                                        <template x-if="discount && discount > 0 && discount < price">
                                            <div class="flex items-center gap-3">
                                                <div class="text-right">
                                                    <div class="text-2xl font-bold" style="color: var(--brand-orange)"
                                                        x-text="'Rp ' + (discount || 0).toLocaleString()"></div>
                                                    <div class="text-sm text-gray-500">Harga Diskon</div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-lg font-medium text-gray-400 line-through"
                                                        x-text="'Rp ' + (price || 0).toLocaleString()"></div>
                                                    <div class="text-xs text-gray-400">Harga Normal</div>
                                                </div>
                                                <div
                                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                                                    Hemat <span
                                                        x-text="Math.round(((price - discount) / price) * 100)"></span>%
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!discount || discount <= 0 || discount >= price">
                                            <div class="text-right">
                                                <div class="text-2xl font-bold" style="color: var(--brand-curious)"
                                                    x-text="'Rp ' + (price || 0).toLocaleString()"></div>
                                                <div class="text-sm text-gray-500">Harga Normal</div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h2 class="form-section-title">Pengaturan Paket</h2>
                            <div class="space-y-4">
                                <div class="p-4 rounded-lg bg-gray-50 border border-gray-200"><label
                                        class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_featured" value="1" x-model="isFeatured"
                                            class="form-checkbox h-5 w-5 text-purple-600 rounded focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-gray-800">Tandai sebagai Paket
                                            Unggulan</span>
                                    </label></div>
                                <div class="p-4 rounded-lg bg-gray-50 border border-gray-200"><label
                                        class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" x-model="isActive"
                                            class="form-checkbox h-5 w-5 text-green-600 rounded focus:ring-green-500">
                                        <span class="ml-3 text-sm font-medium text-gray-800">Paket ini Aktif</span>
                                    </label></div>
                            </div>
                        </section>

                        <div class="flex items-center justify-end gap-4 mt-8 pt-8 border-t border-gray-200">
                            <a href="{{ route('admin.packages.index') }}"
                                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-semibold transition-all">Batal</a>
                            <button type="submit"
                                class="px-8 py-2.5 bg-navy-600 text-white rounded-lg hover:bg-navy-700 font-semibold transition-all shadow-lg hover:shadow-xl transform hover:scale-105"
                                style="background-color:var(--navy-color)"><i class="fas fa-save mr-2"></i>Update
                                Paket</button>
                        </div>
                    </form>
                </div>

                <div class="danger-zone">
                    <h3 class="font-bold text-red-800">Zona Berbahaya</h3>
                    <p class="text-sm text-red-600 mt-1 mb-4">Tindakan ini tidak dapat dibatalkan. Pastikan Anda benar-benar
                        yakin.</p>
                    <button @click="confirmingDelete = true"
                        class="px-5 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all">
                        <i class="fas fa-trash mr-2"></i>Hapus Paket Ini
                    </button>
                </div>
            </div>

            <div class="preview-sticky-container space-y-8">
                <div class="preview-card">
                    <div class="card-header"><i class="fas fa-eye mr-2"></i>Pratinjau Langsung</div>
                    <div class="card-body">
                        <div class="live-package-preview">
                            <div x-show="isFeatured" x-transition class="absolute top-0 right-4 -mt-3"><span
                                    class="px-3 py-1 bg-purple-600 text-white rounded-full text-xs font-bold shadow-lg">⭐
                                    UNGGULAN</span></div>
                            <h3 class="font-bold text-gray-800 text-xl" x-text="name || 'Nama Paket Anda'"></h3>
                            <p class="text-gray-500 text-sm mt-1 h-6"
                                x-text="slots ? slots + ' Slot Frame' : 'Jumlah slot'"></p>

                            <p class="text-xs text-blue-600 font-medium mt-1" x-show="printType === 'strip'">🖨️ Cetak:
                                <span x-text="printCount"></span>x 4R (2 Strip)
                            </p>
                            <p class="text-xs text-blue-600 font-medium mt-1" x-show="printType === 'custom'">🖨️ Cetak:
                                <span x-text="printCount"></span>x 4R (Custom)
                            </p>
                            <p class="text-xs text-green-600 font-medium mt-1" x-show="printType === 'none'">📱 Digital Only
                                (No Print)</p>

                            <div class="my-6">
                                <span class="text-4xl font-bold"
                                    :style="discount > 0 && price > discount ? 'color: var(--brand-orange)' : 'color: var(--brand-curious)'"
                                    x-text="formatPrice(discount && discount < price ? discount : price)"></span>
                                <span x-show="discount > 0 && price > discount"
                                    class="text-lg text-gray-400 line-through ml-2" x-text="formatPrice(price)"></span>
                            </div>
                            <p class="text-sm text-gray-600 h-10"
                                x-text="description || 'Deskripsi singkat paket akan muncul di sini.'"></p>
                            <div class="mt-6 px-4 py-2 rounded-lg" :class="isActive ? 'bg-green-100' : 'bg-gray-100'"><span
                                    class="text-xs font-bold tracking-wider"
                                    :class="isActive ? 'text-green-800' : 'text-gray-600'"
                                    x-text="isActive ? 'TERSEDIA UNTUK PELANGGAN' : 'TIDAK TERSEDIA'"></span></div>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <div class="card-header"><i class="fas fa-chart-bar mr-2"></i>Statistik Paket</div>
                    <div class="card-body space-y-4">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="font-medium text-blue-800">Total Penggunaan</span>
                            <span class="font-bold text-xl text-blue-900">{{ $package->photo_sessions_count ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                            <span class="font-medium text-green-800">Perkiraan Revenue</span>
                            <span class="font-bold text-xl text-green-900">Rp
                                {{ number_format(($package->photo_sessions_count ?? 0) * $package->final_price) }}</span>
                        </div>
                        <div class="text-xs text-gray-500 text-center pt-2">
                            Dibuat: {{ $package->created_at->format('d M Y') }} &bull; Diubah:
                            {{ $package->updated_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="confirmingDelete" @click.self="confirmingDelete = false" x-cloak
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity"
            :class="{'opacity-100': confirmingDelete, 'opacity-0': !confirmingDelete}">
            <div class="bg-white rounded-2xl p-8 max-w-md mx-4 transform transition-transform"
                :class="{'scale-100': confirmingDelete, 'scale-95': !confirmingDelete}">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full mx-auto mb-4 flex items-center justify-center"><i
                            class="fas fa-trash text-2xl text-red-600"></i></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Paket?</h3>
                    <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus paket "{{ $package->name }}"? Tindakan
                        ini tidak dapat dibatalkan.</p>
                    <div class="flex space-x-4">
                        <button @click="confirmingDelete = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">Batal</button>
                        <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 font-semibold transition-all">Ya,
                                Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function packageForm() {
            return {
                // Initialize state with existing data from the $package object,
                // using old() as a fallback in case of validation errors.
                name: '{{ old('name', e($package->name)) }}',
                description: `{{ old('description', e($package->description)) }}`, // Using template literals for multi-line
                slots: {{ old('frame_slots', $package->frame_slots) }},
                printType: '{{ old('print_type', $package->print_type ?? 'strip') }}',
                printCount: {{ old('print_count', $package->print_count ?? 1) }},
                price: {{ old('price', $package->price) }},
                discount: {{ old('discount_price', $package->discount_price ?? 0) }},
                isActive: {{ old('is_active', $package->is_active) ? 'true' : 'false' }},
                isFeatured: {{ old('is_featured', $package->is_featured) ? 'true' : 'false' }},

                isFree: false,
                previousPrice: 0,

                init() {
                    this.isFree = this.price == 0;
                    this.previousPrice = this.price > 0 ? this.price : 50000;
                },

                toggleFree() {
                    if (this.isFree) {
                        this.previousPrice = this.price > 0 ? this.price : this.previousPrice;
                        this.price = 0;
                        this.discount = 0;
                    } else {
                        this.price = this.previousPrice;
                    }
                },

                confirmingDelete: false, // State for the delete modal

                formatPrice(value) {
                    if (typeof value !== 'number' || !value) {
                        return 'Rp 0';
                    }
                    return 'Rp ' + value.toLocaleString('id-ID');
                }
            }
        }
    </script>
@endsection