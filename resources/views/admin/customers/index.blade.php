@extends('admin.layout')

@section('header', 'Data Customer')
@section('description', 'Kelola data customer dan informasi kontak untuk export')

@section('content')
    <div class="space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Customer -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Customer</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $customers->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>

            <!-- Active Customers (With Sessions) -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-teal-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dengan Sesi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::customers()->whereHas('photoSessions')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600">
                    <i class="fas fa-camera text-xl"></i>
                </div>
            </div>

            <!-- New This Month -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-purple-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::customers()->whereMonth('created_at', date('m'))->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>

            <!-- With Email -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Email</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::customers()->whereNotNull('email')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-gray-800">Daftar Customer</h2>

                <form method="GET" class="flex flex-col md:flex-row gap-3 w-full xl:w-auto">
                    <div class="relative flex-grow md:w-64">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama atau email..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <select name="sessions"
                        class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="has_sessions" {{ request('sessions') == 'has_sessions' ? 'selected' : '' }}>Dengan Sesi
                        </option>
                        <option value="no_sessions" {{ request('sessions') == 'no_sessions' ? 'selected' : '' }}>Tanpa Sesi
                        </option>
                    </select>

                    <div class="flex gap-2 w-full md:w-auto">
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                            class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                            class="w-full md:w-auto px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-gray-800 text-white rounded-xl hover:bg-gray-700 transition-colors shadow-sm">
                            <i class="fas fa-filter"></i>
                        </button>
                        @if(request()->hasAny(['search', 'sessions', 'date_from', 'date_to']))
                            <a href="{{ route('admin.customers.index') }}"
                                class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors"
                                title="Reset Filter">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif

                        <!-- Export Actions -->
                        <div class="relative ml-2" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-md hover:bg-blue-700 transition-all flex items-center whitespace-nowrap">
                                <i class="fas fa-download mr-2"></i> Export
                            </button>
                            <div x-show="open"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-20 py-1"
                                style="display: none;" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95">
                                <a href="{{ route('admin.customers.export', request()->all()) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="fas fa-file-csv mr-2 text-green-600"></i> Export CSV
                                </a>
                                <a href="{{ route('admin.customers.emails', array_merge(request()->all(), ['format' => 'text'])) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="fas fa-file-alt mr-2 text-gray-600"></i> List Email (TXT)
                                </a>
                                <button type="button" onclick="copyEmails()"
                                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="fas fa-copy mr-2 text-blue-600"></i> Copy Emails
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Customers List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Status Sesi</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ $customer->name }}</div>
                                            <div class="text-xs text-gray-500">ID: #{{ $customer->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <div class="text-sm text-gray-900 flex items-center">
                                            <i class="far fa-envelope text-gray-400 mr-2 text-xs"></i> {{ $customer->email }}
                                        </div>
                                        @if($customer->phone)
                                            <div class="text-sm text-gray-500 flex items-center mt-1">
                                                <i class="fas fa-phone-alt text-gray-400 mr-2 text-xs"></i> {{ $customer->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($customer->photo_sessions_count > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            {{ $customer->photo_sessions_count }} Sesi
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Last:
                                            {{ $customer->photoSessions->first() ? $customer->photoSessions->first()->created_at->format('d M Y') : '-' }}
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-green-800 border border-gray-200">
                                            Tanpa Sesi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $customer->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                                            <i class="fas fa-user-slash text-2xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-gray-900 font-medium mb-1">Tidak ada customer ditemukan</h3>
                                        <p class="text-gray-500 text-sm">Coba ubah filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            async function copyEmails() {
                try {
                    const response = await fetch('{{ route("admin.customers.emails", request()->all()) }}');
                    const data = await response.json();

                    if (data.emails && data.emails.length > 0) {
                        await navigator.clipboard.writeText(data.emails.join('\n'));
                        // Create a temporary toast/alert
                        const toast = document.createElement('div');
                        toast.className = 'fixed bottom-4 right-4 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce transition-opacity duration-300';
                        toast.style.zIndex = '9999';
                        toast.innerHTML = `<i class="fas fa-check-circle mr-2"></i> ${data.count} email berhasil disalin!`;
                        document.body.appendChild(toast);
                        setTimeout(() => {
                            toast.classList.add('opacity-0');
                            setTimeout(() => toast.remove(), 300);
                        }, 3000);
                    } else {
                        alert('Tidak ada email ditemukan');
                    }
                } catch (error) {
                    console.error('Failed to copy emails:', error);
                    alert('Gagal menyalin email. Silakan coba lagi.');
                }
            }
        </script>
    @endpush
@endsection