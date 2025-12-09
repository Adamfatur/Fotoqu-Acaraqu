@extends('admin.layout')

@section('title', 'Detail Customer')

@section('content')
<style>
    :root{
        --brand-teal:#053a63; --brand-orange:#f29223; --brand-curious:#1a90d6; --brand-dodger:#1fa8f0;
    }
    .brand-primary{ background-color:var(--brand-dodger); color:#fff; }
    .brand-primary:hover{ background-color:var(--brand-curious); }
    .brand-orange{ background-color:var(--brand-orange); color:#1f2937; }
    .brand-orange:hover{ background-color:#e7881c; }
    .link-brand{ color:var(--brand-curious); }
    .link-brand:hover{ color:var(--brand-teal); }
</style>
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Customer</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap customer {{ $customer->name }}</p>
        </div>
        <div class="flex space-x-3">
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.users.edit', $customer) }}" 
                    class="brand-primary px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Customer
            </a>
            @endif
                <a href="{{ route('admin.customers.index') }}" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Customer</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <p class="text-gray-900 text-lg">{{ $customer->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $customer->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <p class="text-gray-900">{{ $customer->phone ?: '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="{{ $customer->getStatusBadgeClass() }}">
                            {{ $customer->getStatusText() }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bergabung</label>
                        <p class="text-gray-900">{{ $customer->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Login</label>
                        <p class="text-gray-900">
                            @if($customer->last_login_at)
                                {{ $customer->last_login_at->format('d M Y, H:i') }}
                            @else
                                Belum pernah login
                            @endif
                        </p>
                    </div>
                    
                    @if($customer->notes)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <p class="text-gray-900">{{ $customer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Photo Sessions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Sesi Foto</h3>
                          <a href="{{ route('admin.sessions.create') }}?customer_id={{ $customer->id }}" 
                              class="brand-primary px-4 py-2 rounded-lg transition-colors flex items-center text-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Sesi Baru
                    </a>
                </div>
                
                @if($customer->photoSessions()->exists())
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photobox</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frame</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($customer->photoSessions()->latest()->with(['photobox', 'package'])->paginate(10) as $session)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $session->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $session->photobox->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $session->package->name ?? '-' }}
                                    <br>
                                    <span class="text-xs text-gray-500">{{ $session->package->slot_count ?? 0 }} slot</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $session->session_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($session->session_status === 'active' ? 'bg-blue-100 text-blue-800' : 
                                           ($session->session_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($session->session_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $session->photos()->count() }} foto
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $session->frame ? '1' : '0' }} frame
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.sessions.show', $session) }}" 
                                                    class="link-brand mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($session->session_status !== 'completed' && auth()->user()->hasRole(['admin', 'manager']))
                                                <a href="{{ route('admin.sessions.edit', $session) }}" 
                                                    class="link-brand">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($customer->photoSessions()->paginate(10)->hasPages())
                <div class="mt-6">
                    {{ $customer->photoSessions()->paginate(10)->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-camera text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Sesi Foto</h3>
                    <p class="text-gray-500 mb-4">Customer ini belum memiliki sesi foto apapun.</p>
                    <a href="{{ route('admin.sessions.create') }}?customer_id={{ $customer->id }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Sesi Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Sesi</span>
                        <span class="font-semibold text-gray-900">{{ $customer->photoSessions()->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sesi Completed</span>
                        <span class="font-semibold text-green-600">{{ $customer->photoSessions()->where('session_status', 'completed')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sesi Pending</span>
                        <span class="font-semibold text-yellow-600">{{ $customer->photoSessions()->where('session_status', 'pending')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Foto</span>
                        <span class="font-semibold text-gray-900">{{ $customer->photoSessions()->withCount('photos')->get()->sum('photos_count') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Frame</span>
                        <span class="font-semibold text-gray-900">{{ $customer->photoSessions()->has('frame')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Revenue Stats -->
            @php
                $totalRevenue = $customer->photoSessions()
                    ->whereNotNull('package_id')
                    ->where('session_status', 'completed')
                    ->with('package')
                    ->get()
                    ->sum(function($session) {
                        return $session->package->price ?? 0;
                    });
            @endphp
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue</h3>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 mb-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </div>
                    <p class="text-gray-500 text-sm">Total revenue dari customer ini</p>
                </div>
            </div>

            <!-- Actions -->
            @if(auth()->user()->hasRole('admin'))
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    @if($customer->status === 'active')
                        <form action="{{ route('admin.users.ban', $customer) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin menonaktifkan customer ini?')"
                                    class="w-full brand-orange px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-ban mr-2"></i>
                                Nonaktifkan
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.users.unban', $customer) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full brand-primary px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-check mr-2"></i>
                                Aktifkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Kontak</h3>
                <div class="space-y-3">
                    @if($customer->email)
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-gray-400 w-5 mr-3"></i>
                                <a href="mailto:{{ $customer->email }}" 
                                    class="link-brand text-sm break-all">
                            {{ $customer->email }}
                        </a>
                    </div>
                    @endif
                    
                    @if($customer->phone)
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 w-5 mr-3"></i>
                                <a href="tel:{{ $customer->phone }}" 
                                    class="link-brand text-sm">
                            {{ $customer->phone }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
