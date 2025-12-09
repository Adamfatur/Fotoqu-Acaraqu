@extends('admin.layout')

@section('header', 'Mulai Event Baru')
@section('description', 'Konfigurasi event spesial untuk photobox')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('admin.events.store') }}" method="POST" class="space-y-6"
                x-data="{ eventType: 'digital', packageId: '' }">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Event</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Wedding John & Doe"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="photobox_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Photobox</label>
                    <select name="photobox_id" id="photobox_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Photobox Aktif --</option>
                        @foreach($photoboxes as $photobox)
                            <option value="{{ $photobox->id }}">
                                {{ $photobox->name }} ({{ $photobox->code }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Hanya photobox dengan status 'active' yang muncul di sini.</p>
                </div>

                <!-- Event Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Event</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label
                            class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                            :class="eventType === 'digital' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300'">
                            <input type="radio" name="event_type" value="digital" class="sr-only" x-model="eventType">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Unlimited Digital (No
                                        Print)</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Dibatasi Durasi Waktu</span>
                                </span>
                            </span>
                            <i class="fas fa-check-circle text-blue-600" x-show="eventType === 'digital'"></i>
                        </label>

                        <label
                            class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none"
                            :class="eventType === 'print' ? 'border-blue-500 ring-2 ring-blue-500' : 'border-gray-300'">
                            <input type="radio" name="event_type" value="print" class="sr-only" x-model="eventType">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900">Unlimited Digital + Cetak</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500">Dibatasi Durasi & Jumlah
                                        Cetak</span>
                                </span>
                            </span>
                            <i class="fas fa-check-circle text-blue-600" x-show="eventType === 'print'"></i>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="package_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Template / Layout
                        (dari Paket)</label>
                    <select name="package_id" id="package_id" required x-model="packageId"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Template Paket --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" data-print-type="{{ $package->print_type }}"
                                x-show="eventType === 'digital' ? true : '{{ $package->print_type }}' !== 'none'">
                                {{ $package->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1" x-show="eventType === 'digital'">
                        Layout frame dan setting kamera akan diambil dari paket ini. Fitur cetak otomatis
                        <strong>dinonaktifkan</strong>.
                    </p>
                    <p class="text-sm text-gray-500 mt-1" x-show="eventType === 'print'">
                        Hanya menampilkan paket yang memiliki fitur cetak.
                    </p>
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Durasi Event
                        (Menit)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" required min="1"
                        placeholder="Contoh: 120 (untuk 2 jam)"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Event akan otomatis berhenti setelah durasi ini habis.</p>
                </div>

                <div x-show="eventType === 'print'" x-transition>
                    <label for="print_quota" class="block text-sm font-medium text-gray-700 mb-1">Quota Cetak
                        (Lembar)</label>
                    <input type="number" name="print_quota" id="print_quota" min="1" placeholder="Contoh: 100"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">
                        Jika quota habis, event tetap berjalan namun mode cetak akan dinonaktifkan (kembali ke Digital
                        Only).
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.events.index') }}"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md">
                        Mulai Event Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection