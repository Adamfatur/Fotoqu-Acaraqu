@extends('admin.layout')

@section('header', 'Buat Sesi Foto Baru')
@section('description', 'Daftarkan customer baru untuk sesi foto photobox')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Progress Steps -->
    <!-- Simple Header -->
    <div class="bg-white rounded-2xl p-6 card-shadow border border-gray-100 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Sesi Foto Baru</h1>
            <p class="text-gray-600 mt-1">Isi formulir berikut untuk mendaftarkan pelanggan</p>
        </div>
        <div class="hidden sm:block">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-camera text-blue-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="bg-white rounded-2xl p-8 card-shadow border border-gray-100">
        <form method="POST" action="{{ route('admin.sessions.store') }}" class="space-y-8">
            @csrf

            <!-- Customer Information -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Informasi Customer</h2>
                        <p class="text-gray-600">Data customer yang akan menggunakan photobox</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2" style="color: #1a90d6"></i>
                            Nama Lengkap *
                        </label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 @error('customer_name') border-red-500 @enderror"
                               placeholder="Masukkan nama lengkap customer" required>
                        @error('customer_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2" style="color: #1a90d6"></i>
                            Email Address
                        </label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 @error('customer_email') border-red-500 @enderror"
                               placeholder="customer@email.com" id="customer_email">
                        @error('customer_email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <div class="mt-3">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="send_email" value="1" {{ old('send_email') ? 'checked' : '' }} 
                                       class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded"
                                       id="email_enabled">
                                <span class="ml-2 text-sm text-gray-600">Kirim frame hasil ke email customer</span>
                            </label>
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jika dicentang, frame akan dikirim ke email di atas
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photobox Selection -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #1a90d6, #1fa8f0); color: #fff;">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Pilih Photobox</h2>
                        <p class="text-gray-600">Photobox yang akan digunakan untuk sesi foto</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($photoboxes as $photobox)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="photobox_id" value="{{ $photobox->id }}" 
                               class="sr-only peer" {{ (old('photobox_id') == $photobox->id || $photoboxes->count() == 1) ? 'checked' : '' }} required>
                        <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 peer-checked:ring-sky-400 peer-checked:shadow-md hover:border-sky-300 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold" style="background: linear-gradient(135deg, #1a90d6, #1fa8f0)">
                                    {{ substr($photobox->code, -2) }}
                                </div>
                                <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-sky-500 peer-checked:bg-sky-500 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $photobox->code }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $photobox->name }}</p>
                                <p class="text-xs text-gray-500">📍 {{ $photobox->location }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                        <span class="text-xs text-green-600 font-medium">Aktif</span>
                                    </div>
                                    @php
                                        $queueCount = $photobox->photoSessions()
                                            ->whereIn('session_status', ['approved','in_progress','photo_selection','processing'])
                                            ->count();
                                    @endphp
                                    <span class="text-xs text-gray-600">Antrian: {{ max(0, $queueCount - ($queueCount>0 ? 1 : 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </label>
                    @empty
                    <div class="col-span-full p-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-box text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak Ada Photobox Aktif</h3>
                        <p class="text-gray-500">Tidak ada photobox dengan status aktif saat ini</p>
                    </div>
                    @endforelse
                </div>
                @error('photobox_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Package Selection -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #1a90d6, #1fa8f0); color: #fff;">
                        <i class="fas fa-gift text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Pilih Paket Foto</h2>
                        <p class="text-gray-600">Paket foto yang tersedia dengan berbagai slot</p>
                    </div>
                </div>

                @if($packages->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($packages as $package)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="package_id" value="{{ $package->id }}" 
                                   class="sr-only peer" {{ old('package_id') == $package->id ? 'checked' : '' }} required>
                            <div class="p-6 border-2 border-gray-200 rounded-xl peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 peer-checked:ring-sky-400 peer-checked:shadow-md hover:border-sky-300 transition-all duration-200 relative">
                                <!-- Selected checkmark -->
                                <div class="absolute top-2 left-2 hidden items-center justify-center w-6 h-6 rounded-full bg-sky-500 text-white shadow peer-checked:flex">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <div class="text-center">
                                    <!-- Package Icon based on slots -->
                                    @php
                                        $iconColor = match($package->frame_slots) {
                                            '4' => 'from-sky-400 to-sky-600',
                                            '6' => 'from-cyan-400 to-cyan-600', 
                                            '8' => 'from-blue-400 to-blue-600',
                                            default => 'from-gray-400 to-gray-600'
                                        };
                                        $previewColor = match($package->frame_slots) {
                                            '4' => 'bg-sky-200',
                                            '6' => 'bg-cyan-200',
                                            '8' => 'bg-blue-200',
                                            default => 'bg-gray-200'
                                        };
                                    @endphp
                                    <div class="w-16 h-16 bg-gradient-to-br {{ $iconColor }} rounded-xl flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4">
                                        {{ $package->frame_slots }}
                                    </div>
                                    
                                    <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $package->name }}</h3>
                                    <p class="text-sm text-gray-600 mb-3">{{ $package->description }}</p>
                                    
                                    <!-- Price Display -->
                                    <div class="mb-3">
                                        @if($package->discount_price && $package->discount_price < $package->price)
                                            <div class="text-lg line-through text-gray-400">
                                                Rp {{ number_format($package->price, 0, ',', '.') }}
                                            </div>
                                            <div class="text-2xl font-bold" style="color: #1a90d6">
                                                Rp {{ number_format($package->discount_price, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-green-600 font-medium">
                                                Hemat {{ number_format($package->price - $package->discount_price, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <div class="text-2xl font-bold" style="color: #1a90d6">
                                                Rp {{ number_format($package->price, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Frame Preview Grid -->
                                    @php
                                        $slots = (int) $package->frame_slots;
                                        $gridClass = '';
                                        $heightClass = '';

                                        if ($slots === 4) {
                                            $gridClass = 'grid-cols-2';
                                            $heightClass = 'h-12'; // 2 rows
                                        } elseif ($slots === 6) {
                                            $gridClass = 'grid-cols-2';
                                            $heightClass = 'h-16'; // 3 rows
                                        } elseif ($slots === 8) {
                                            $gridClass = 'grid-cols-2';
                                            $heightClass = 'h-20'; // 4 rows
                                        }
                                    @endphp

                                    @if($gridClass)
                                        <div class="grid {{ $gridClass }} gap-1 w-12 {{ $heightClass }} mx-auto">
                                            @for($i = 0; $i < $slots; $i++)
                                                <div class="{{ $previewColor }} rounded"></div>
                                            @endfor
                                        </div>
                                    @endif
                                    
                                    <!-- Features -->
                                    @if($package->features && is_array($package->features) && count($package->features) > 0)
                                        <div class="mt-3 text-xs text-gray-600">
                                            @foreach($package->features as $feature)
                                                <div class="flex items-center justify-center mb-1">
                                                    <i class="fas fa-check text-green-500 mr-1"></i>
                                                    {{ $feature }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if($package->is_featured)
                                        <div class="absolute top-2 right-2">
                                            <span class="text-white text-xs font-bold px-2 py-1 rounded-full" style="background: linear-gradient(90deg, #f29223, #f5a84f)">
                                                POPULER
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-xl">
                        <i class="fas fa-gift text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Tidak Ada Paket Tersedia</h3>
                        <p class="text-gray-500">Silakan buat paket foto terlebih dahulu di menu paket</p>
                    </div>
                @endif
                
                @error('package_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Information -->
            <div>
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-credit-card text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Konfirmasi Pembayaran</h2>
                        <p class="text-gray-600">Proses pembayaran customer sekaligus</p>
                    </div>
                </div>

                <!-- Hidden message for free package -->
                <div id="free_package_message" class="hidden mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-gift text-2xl mr-3"></i>
                        <div>
                            <h3 class="font-bold">Paket Gratis Dipilih!</h3>
                            <p class="text-sm">Anda memilih paket gratis. Tidak diperlukan pembayaran.</p>
                        </div>
                    </div>
                </div>

                <div id="payment_details_section" class="grid grid-cols-1 md:grid-cols-2 gap-6 transition-all duration-300">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-money-bill-wave mr-2" style="color:#1a90d6"></i>
                            Metode Pembayaran *
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(config('fotoku.payment_methods') as $key => $method)
                            <label class="relative cursor-pointer block">
                                <input type="radio" name="payment_method" value="{{ $key }}" 
                                       class="sr-only peer" {{ old('payment_method') == $key ? 'checked' : '' }} required>
                                
                                <!-- Enhanced payment method card with stronger visual indicators -->
                                <div class="p-4 border-2 border-gray-200 rounded-xl 
                                    hover:border-sky-300 transition-all duration-200 text-center relative overflow-hidden
                                    peer-checked:border-sky-500 peer-checked:bg-sky-50 peer-checked:ring-2 peer-checked:ring-sky-500
                                    hover:shadow-md">
                                    

                                    
                                    <!-- Checkmark -->
                                    <div class="absolute top-2 right-2 bg-sky-500 text-white rounded-full w-5 h-5 hidden items-center justify-center peer-checked:flex transition-opacity">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>
                                    
                                    <!-- Icon with enhanced effects -->
                                    @if($key === 'free')
                                        <div class="w-12 h-12 mx-auto flex items-center justify-center rounded-full mb-2 transition-all duration-300" style="background: rgba(26,144,214,.08)">
                                            <i class="fas fa-gift text-2xl" style="color:#1a90d6"></i>
                                        </div>
                                    @elseif($key === 'qris')
                                        <div class="w-12 h-12 mx-auto flex items-center justify-center rounded-full mb-2 transition-all duration-300" style="background: rgba(26,144,214,.08)">
                                            <i class="fas fa-qrcode text-2xl" style="color:#1a90d6"></i>
                                        </div>
                                    @elseif($key === 'edc')
                                        <div class="w-12 h-12 mx-auto flex items-center justify-center rounded-full mb-2 transition-all duration-300" style="background: rgba(26,144,214,.08)">
                                            <i class="fas fa-credit-card text-2xl" style="color:#1a90d6"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Method name with enhanced visibility -->
                                    <p class="text-sm font-medium peer-checked:font-bold text-gray-800" style="--tw-text-opacity:1;">
                                        {{ $method }}
                                    </p>
                                    

                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error('payment_method')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calculator mr-2 text-green-500"></i>
                            Jumlah Pembayaran *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" name="payment_amount" value="{{ old('payment_amount') }}" 
                                   class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 @error('payment_amount') border-red-500 @enderror"
                                   placeholder="0" required id="payment_amount">
                        </div>
                        @error('payment_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Akan terisi otomatis saat memilih paket
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-2 text-green-500"></i>
                        Catatan Pembayaran (Opsional)
                    </label>
                    <textarea name="payment_notes" rows="2" 
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 @error('payment_notes') border-red-500 @enderror"
                              placeholder="Catatan tambahan untuk pembayaran...">{{ old('payment_notes') }}</textarea>
                    @error('payment_notes')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sticky-note mr-2 text-purple-500"></i>
                    Catatan Sesi (Opsional)
                </label>
                <textarea name="notes" rows="3" 
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all duration-200 @error('notes') border-red-500 @enderror"
                          placeholder="Tambahkan catatan khusus untuk sesi ini...">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.sessions.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>

        <button type="submit" 
            class="inline-flex items-center px-8 py-3 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200" style="background: linear-gradient(90deg, #1a90d6, #1fa8f0)">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Sesi Foto
                </button>
            </div>
        </form>
    </div>

    <!-- Information Panel -->
    <div class="rounded-2xl p-6" style="background: rgba(26,144,214,.08); border: 1px solid rgba(26,144,214,.25)">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #1a90d6, #1fa8f0); color: #fff;">
                <i class="fas fa-info text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold mb-2" style="color: #053a63">Informasi Penting</h3>
                <ul class="text-sm space-y-1" style="color: #1a90d6">
                    <li>• Sistem akan mengambil {{ config('fotoku.total_photos', 3) }} foto secara otomatis</li>
                    <li>• Setelah pengambilan, customer memilih 3 foto terbaik; frame fotostrip 6 slot akan menduplikasi 3 foto tersebut</li>
                    <li>• Frame akan dikirim ke email customer jika opsi "Kirim email" diaktifkan</li>
                    <li>• Pembayaran diproses saat pembuatan; sesi disetujui otomatis dan siap digunakan di photobox (aktif saat dimulai di perangkat)</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Simpler, cleaner JS implementation
document.addEventListener('DOMContentLoaded', function() {
    // Core elements
    const packageInputs = document.querySelectorAll('input[name="package_id"]');
    const emailCheckbox = document.getElementById('email_enabled');
    const emailField = document.getElementById('customer_email');
    const paymentAmountInput = document.getElementById('payment_amount');
    const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
    
    console.log('Element check:');
    console.log('- Package inputs found:', packageInputs.length);
    console.log('- Email checkbox found:', emailCheckbox ? 'yes' : 'no');
    console.log('- Payment method inputs found:', paymentMethodInputs.length);
    
    // Package prices from server
    const packagePrices = {
        @foreach($packages as $package)
        '{{ $package->id }}': {{ $package->discount_price ?: $package->price }},
        @endforeach
    };
    
    // Format currency helper (IDR)
    function formatCurrency(amount) {
        return parseInt(amount).toLocaleString('id-ID');
    }
    
    // ----- PACKAGE SELECTION -----
    // Add click event directly to the labels for better UX
    const packageLabels = document.querySelectorAll('label:has(input[name="package_id"])');
    
    // Fallback untuk browser yang tidak support :has selector
    if (packageLabels.length === 0) {
        const allLabels = document.querySelectorAll('label');
        allLabels.forEach(label => {
            const input = label.querySelector('input[name="package_id"]');
            if (input) {
                label.addEventListener('click', function() {
                    setTimeout(() => {
                        if (input.checked) {
                            updatePaymentFromPackage(input.value);
                        }
                    }, 50);
                });
            }
        });
    } else {
        packageLabels.forEach(label => {
            label.addEventListener('click', function() {
                setTimeout(() => {
                    const input = this.querySelector('input[name="package_id"]');
                    if (input && input.checked) {
                        updatePaymentFromPackage(input.value);
                    }
                }, 50); // Small timeout to ensure radio is checked
            });
        });
    }
    
    // Handle package change
    function updatePaymentFromPackage(packageId) {
        if (!paymentAmountInput) return;
        
        const packagePrice = packagePrices[packageId];
        if (packagePrice !== undefined) {
            // Set price in payment field
            paymentAmountInput.value = formatCurrency(packagePrice);
            
            // Auto-select payment method based on price
            if (packagePrice === 0) {
                // Free package = auto-select free payment method
                selectPaymentMethod('free');
                
                // Toggle visibility: Hide inputs, show message
                document.getElementById('payment_details_section').classList.add('hidden');
                document.getElementById('free_package_message').classList.remove('hidden');
            } else {
                // Paid package
                paymentAmountInput.readOnly = false;
                paymentAmountInput.classList.remove('bg-gray-100');
                
                // Toggle visibility: Show inputs, hide message
                document.getElementById('payment_details_section').classList.remove('hidden');
                document.getElementById('free_package_message').classList.add('hidden');
                
                // HIDE the 'Free' payment option completely when a paid package is selected
                const freeOption = document.querySelector('input[name="payment_method"][value="free"]');
                if (freeOption) {
                    const freeLabel = freeOption.closest('label');
                    if (freeLabel) freeLabel.classList.add('hidden');
                }
                
                // Normalize UI: Ensure other options are visible
                document.querySelectorAll('input[name="payment_method"]:not([value="free"])').forEach(input => {
                    const label = input.closest('label');
                    if (label) label.classList.remove('hidden');
                });

                // Set default to QRIS if current selection is 'free' (which is now hidden) or 'null'
                const currentMethod = document.querySelector('input[name="payment_method"]:checked');
                if (!currentMethod || currentMethod.value === 'free') {
                    selectPaymentMethod('qris');
                }
            }
        }
    }
    
    // ----- EMAIL HANDLING -----
    if (emailCheckbox && emailField) {
        // Toggle email field based on checkbox
        function toggleEmailField() {
            const parent = emailField.closest('div');
            
            if (emailCheckbox.checked) {
                emailField.disabled = false;
                emailField.required = true;
                emailField.classList.remove('bg-gray-100');
                if (parent) parent.style.opacity = '1';
            } else {
                emailField.disabled = true;
                emailField.required = false;
                emailField.value = '';
                emailField.classList.add('bg-gray-100');
                if (parent) parent.style.opacity = '0.6';
            }
        }
        
        // Initial state
        toggleEmailField();
        
        // Email checkbox changes
        emailCheckbox.addEventListener('change', toggleEmailField);
    }
    
    // ----- PAYMENT METHOD -----
    // Payment method selection handler
    function selectPaymentMethod(method) {
        const input = document.querySelector(`input[name="payment_method"][value="${method}"]`);
        if (input) {
            // Reset all payment method visual states (not inputs directly)
            document.querySelectorAll('input[name="payment_method"]').forEach(input => {
                const parentLabel = input.closest('label');
                if (parentLabel) {
                    // Reset border and background
                    parentLabel.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-50');
                    
                    // Reset icon container
                    const iconContainer = parentLabel.querySelector('.w-12');
                    if (iconContainer) {
                        iconContainer.classList.remove('bg-green-100', 'bg-blue-100', 'bg-purple-100');
                    }
                    
                    // Reset text styling
                    const methodText = parentLabel.querySelector('p');
                    if (methodText) {
                        methodText.classList.remove('font-bold', 'text-green-700', 'text-blue-700', 'text-purple-700');
                    }
                    
                    // Hide check icon
                    const checkIcon = parentLabel.querySelector('.absolute.top-2.right-2');
                    if (checkIcon) {
                        checkIcon.classList.add('hidden');
                    }
                }
            });
            
            // Check this payment method input if not already checked
            if (!input.checked) {
                input.checked = true;
            }
            
            // Update visual indicators for the selected payment method
            const parentLabel = input.closest('label');
            if (parentLabel) {
                // Highlight the entire label
                parentLabel.classList.add('ring-2', 'ring-purple-500', 'bg-purple-50');
                
                // Customize icon container based on payment method
                const iconContainer = parentLabel.querySelector('.w-12');
                if (iconContainer) {
                    if (method === 'free') {
                        iconContainer.classList.add('bg-green-100');
                    } else if (method === 'qris') {
                        iconContainer.classList.add('bg-blue-100');
                    } else if (method === 'edc') {
                        iconContainer.classList.add('bg-purple-100');
                    }
                }
                
                // Style the method text
                const methodText = parentLabel.querySelector('p');
                if (methodText) {
                    methodText.classList.add('font-bold');
                    if (method === 'free') {
                        methodText.classList.add('text-green-700');
                    } else if (method === 'qris') {
                        methodText.classList.add('text-blue-700');
                    } else if (method === 'edc') {
                        methodText.classList.add('text-purple-700');
                    }
                }
                
                // Show check icon
                const checkIcon = parentLabel.querySelector('.absolute.top-2.right-2');
                if (checkIcon) {
                    checkIcon.classList.remove('hidden');
                }
            }
            
            // Handle payment amount field state
            if (method === 'free') {
                // Always ensure zero value for free method and make readonly
                paymentAmountInput.value = '0';
                paymentAmountInput.readOnly = true;
                paymentAmountInput.classList.add('bg-gray-100');
                
                // Set a hidden field to ensure this value is sent properly
                let hiddenZeroField = document.getElementById('zero_amount_flag');
                if (!hiddenZeroField) {
                    hiddenZeroField = document.createElement('input');
                    hiddenZeroField.type = 'hidden';
                    hiddenZeroField.id = 'zero_amount_flag';
                    hiddenZeroField.name = 'zero_amount';
                    hiddenZeroField.value = '1';
                    paymentAmountInput.parentNode.appendChild(hiddenZeroField);
                }
            } else {
                paymentAmountInput.readOnly = false;
                paymentAmountInput.classList.remove('bg-gray-100');
                
                // Remove any hidden zero field if it exists
                const hiddenZeroField = document.getElementById('zero_amount_flag');
                if (hiddenZeroField) {
                    hiddenZeroField.parentNode.removeChild(hiddenZeroField);
                }
                
                // Restore package price if any package is selected
                const selectedPackage = document.querySelector('input[name="package_id"]:checked');
                if (selectedPackage) {
                    const packagePrice = packagePrices[selectedPackage.value];
                    if (packagePrice && packagePrice > 0) {
                        paymentAmountInput.value = formatCurrency(packagePrice);
                    }
                }
            }
            
            // Log for debugging
            console.log(`Payment method selected: ${method}`);
        }
    }
    
    // Add click event to payment method labels for better UX
    const paymentLabels = document.querySelectorAll('label:has(input[name="payment_method"])');
    
    // Buat fungsi yang lebih sederhana untuk menghindari browser compatibility issues dengan :has()
    if (paymentLabels.length === 0) {
        const allLabels = document.querySelectorAll('label');
        allLabels.forEach(label => {
            const input = label.querySelector('input[name="payment_method"]');
            if (input) {
                label.addEventListener('click', function() {
                    setTimeout(() => {
                        if (input.checked) {
                            selectPaymentMethod(input.value);
                        }
                    }, 50);
                });
            }
        });
    } else {
        paymentLabels.forEach(label => {
            label.addEventListener('click', function() {
                setTimeout(() => {
                    const input = this.querySelector('input[name="payment_method"]');
                    if (input && input.checked) {
                        selectPaymentMethod(input.value);
                    }
                }, 50);
            });
        });
    }
    
    // Direct event listeners on radio buttons as primary handler
    paymentMethodInputs.forEach(input => {
        // Add change event
        input.addEventListener('change', function() {
            if (this.checked) {
                selectPaymentMethod(this.value);
            }
        });
        
        // Also add click event for better response
        input.addEventListener('click', function(e) {
            // Small timeout to ensure state is updated
            setTimeout(() => {
                if (this.checked) {
                    selectPaymentMethod(this.value);
                }
            }, 50);
        });
    });
    
    // Direct event listeners on package inputs as backup
    packageInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                updatePaymentFromPackage(this.value);
            }
        });
    });
    
    // ----- FORM VALIDATION -----
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedPackage = document.querySelector('input[name="package_id"]:checked');
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
            const paymentAmount = paymentAmountInput ? paymentAmountInput.value.replace(/[^\d]/g, '') : '';
            
            // Required validations
            if (!selectedPackage) {
                e.preventDefault();
                alert('Silakan pilih paket foto terlebih dahulu');
                return;
            }
            
            if (!selectedPaymentMethod) {
                e.preventDefault();
                alert('Silakan pilih metode pembayaran terlebih dahulu');
                return;
            }
            
            // Payment amount validation
            if (selectedPaymentMethod && selectedPaymentMethod.value !== 'free' && (!paymentAmount || paymentAmount === '0')) {
                e.preventDefault();
                alert('Silakan masukkan jumlah pembayaran');
                paymentAmountInput.focus();
                return;
            } 
            
            // Make sure payment amount is 0 for free payment method and create hidden field if needed
            if (selectedPaymentMethod && selectedPaymentMethod.value === 'free') {
                // Always force 0 for free method
                paymentAmountInput.value = '0';
                
                // Ensure we have a hidden field for submission
                let hiddenZeroField = document.getElementById('zero_amount_flag');
                if (!hiddenZeroField) {
                    hiddenZeroField = document.createElement('input');
                    hiddenZeroField.type = 'hidden';
                    hiddenZeroField.id = 'zero_amount_flag';
                    hiddenZeroField.name = 'zero_amount';
                    hiddenZeroField.value = '1';
                    form.appendChild(hiddenZeroField);
                }
                
                console.log('Submitting free session with zero amount');
            }
            
            // Email validation when enabled
            if (emailCheckbox && emailCheckbox.checked && emailField) {
                if (!emailField.value.trim()) {
                    e.preventDefault();
                    alert('Silakan masukkan email customer');
                    emailField.focus();
                    return;
                }
            }
        });
    }
    
    // Format payment amount as currency
    if (paymentAmountInput) {
        paymentAmountInput.addEventListener('input', function() {
            if (!this.readOnly) {
                let value = this.value.replace(/[^\d]/g, '');
                if (value) {
                    this.value = formatCurrency(value);
                }
            }
        });
        
        // Add hidden field to store numeric value for server processing
        const hiddenAmountInput = document.createElement('input');
        hiddenAmountInput.type = 'hidden';
        hiddenAmountInput.name = 'payment_amount_numeric';
        paymentAmountInput.parentNode.appendChild(hiddenAmountInput);
        
        // Update hidden field when payment amount changes
        paymentAmountInput.addEventListener('input', function() {
            const numericValue = this.value.replace(/[^\d]/g, '');
            hiddenAmountInput.value = numericValue;
        });
    }
    
    // Initialize values on page load (if any selections are pre-selected)
    setTimeout(function() {
        // Check if package is pre-selected
        const preSelectedPackage = document.querySelector('input[name="package_id"]:checked');
        if (preSelectedPackage) {
            console.log('Pre-selected package found:', preSelectedPackage.value);
            updatePaymentFromPackage(preSelectedPackage.value);
        }
        
        // Check if payment method is pre-selected
        const preSelectedPayment = document.querySelector('input[name="payment_method"]:checked');
        if (preSelectedPayment) {
            console.log('Pre-selected payment method found:', preSelectedPayment.value);
            selectPaymentMethod(preSelectedPayment.value);
        } else {
            // If no payment method is selected but we have a package price > 0,
            // select the default non-free payment method (QRIS)
            if (preSelectedPackage) {
                const packagePrice = packagePrices[preSelectedPackage.value];
                if (packagePrice && packagePrice > 0) {
                    selectPaymentMethod('qris');
                } else {
                    // For free packages, select 'free' payment method
                    selectPaymentMethod('free');
                }
            }
        }
        
        // Setup initial email field state
        if (emailCheckbox) {
            const toggleEvent = new Event('change');
            emailCheckbox.dispatchEvent(toggleEvent);
        }
    }, 100);
});
</script>
@endpush
@endsection
