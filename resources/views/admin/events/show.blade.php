@extends('admin.layout')

@section('header', 'Detail Event')
@section('description', 'Monitor status dan statistik event')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Status Event</h3>
                <p class="text-xs text-gray-400 mt-1">{{ $event->photobox->name ?? 'Semua Photobox' }}</p>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-2xl font-bold {{ $event->status === 'active' ? 'text-green-600' : 'text-gray-600' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                    @if($event->status === 'active')
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    @endif
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ is_null($event->print_quota) || $event->print_quota == 0 ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        <i
                            class="fas {{ is_null($event->print_quota) || $event->print_quota == 0 ? 'fa-cloud-upload-alt' : 'fa-print' }} mr-1.5"></i>
                        {{ is_null($event->print_quota) || $event->print_quota == 0 ? 'Digital Only' : 'Print Enabled' }}
                    </span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Total Sesi</h3>
                <div class="mt-2 text-3xl font-bold text-gray-900">{{ $totalSessions }}</div>
                <p class="text-xs text-gray-500 mt-1">{{ $completedSessions }} Selesai</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Penggunaan Cetak</h3>
                <div class="mt-2">
                    <span class="text-3xl font-bold text-blue-600">{{ $event->prints_used }}</span>
                    @if(!is_null($event->print_quota))
                        <span class="text-gray-400 text-xl">/ {{ $event->print_quota }}</span>
                    @else
                        <span class="text-gray-400 text-sm"> (Unlimited)</span>
                    @endif
                </div>
                @if(!is_null($event->print_quota) && $event->print_quota > 0)
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full"
                            style="width: {{ min(100, ($event->prints_used / $event->print_quota) * 100) }}%"></div>
                    </div>
                @endif
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-medium text-gray-500">Waktu Event</h3>
                <div class="mt-2">
                    <span
                        class="text-xl font-bold text-gray-900">{{ $event->active_until ? $event->active_until->format('H:i') : '∞' }}</span>
                    <span class="text-sm text-gray-500">WIB</span>
                </div>
                @if($event->status === 'active' && $event->active_until)
                    <p class="text-xs text-blue-600 mt-1 font-medium">
                        Berakhir {{ $event->active_until->diffForHumans() }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @if($event->status === 'active')
            <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Kontrol Event</h3>
                    <p class="text-sm text-gray-500">Hentikan event jika acara sudah selesai. Sesi baru tidak akan dibuat
                        otomatis lagi.</p>
                </div>
                <div class="flex gap-3">
                    <form action="{{ route('admin.events.create-session', $event) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md font-medium">
                            <i class="fas fa-plus mr-2"></i>Buat Sesi Manual
                        </button>
                    </form>
                    <form action="{{ route('admin.events.stop', $event) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghentikan event ini?');">
                        @csrf
                        <button type="submit"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 shadow-md font-medium">
                            <i class="fas fa-stop-circle mr-2"></i>Hentikan Event
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Recent Sessions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Sesi Terakhir</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Sesi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($event->photoSessions as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $session->session_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                    {{ $session->session_status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $session->session_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $session->created_at->format('H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.sessions.show', $session) }}"
                                    class="text-blue-600 hover:text-blue-900">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Belum ada sesi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection