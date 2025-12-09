@extends('admin.layout')

@section('title', 'Detail Pengguna')

@section('content')
<style>
    :root{ --brand-teal:#053a63; --brand-orange:#f29223; --brand-curious:#1a90d6; --brand-dodger:#1fa8f0; }
    .brand-primary{ background-color:var(--brand-dodger); color:#fff; }
    .brand-primary:hover{ background-color:var(--brand-curious); }
    .brand-orange{ background-color:var(--brand-orange); color:#1f2937; }
    .brand-orange:hover{ background-color:#e7881c; }
    .link-brand{ color:var(--brand-curious); }
    .link-brand:hover{ color:var(--brand-teal); }
</style>
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pengguna</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap pengguna {{ $user->name }}</p>
        </div>
        <div class="flex space-x-3">
                <a href="{{ route('admin.users.edit', $user) }}" 
                    class="brand-primary px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Informasi Pengguna</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <p class="text-gray-900 text-lg">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <span class="{{ $user->getRoleBadgeClass() }}">
                            {{ $user->getRoleText() }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="{{ $user->getStatusBadgeClass() }}">
                            {{ $user->getStatusText() }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <p class="text-gray-900">{{ $user->phone ?: '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Login</label>
                        <p class="text-gray-900">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->format('d M Y, H:i') }}
                            @else
                                Belum pernah login
                            @endif
                        </p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <p class="text-gray-900">{{ $user->notes ?: '-' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bergabung</label>
                        <p class="text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Terakhir Update</label>
                        <p class="text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions & Stats Card -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    @if($user->id !== auth()->id())
                        @if($user->status === 'active')
                            <form action="{{ route('admin.users.ban', $user) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Yakin ingin menonaktifkan pengguna ini?')"
                                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                    <i class="fas fa-ban mr-2"></i>
                                    Nonaktifkan
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                    <i class="fas fa-check mr-2"></i>
                                    Aktifkan
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin menghapus pengguna ini? Aksi ini tidak dapat dibatalkan!')"
                                    class="w-full bg-red-800 hover:bg-red-900 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus Permanen
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            @if($user->role === 'customer')
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Sesi</span>
                        <span class="font-semibold text-gray-900">{{ $user->photoSessions()->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sesi Completed</span>
                        <span class="font-semibold text-green-600">{{ $user->photoSessions()->where('session_status', 'completed')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Foto</span>
                        <span class="font-semibold text-gray-900">{{ $user->photoSessions()->withCount('photos')->get()->sum('photos_count') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total Frame</span>
                        <span class="font-semibold text-gray-900">{{ $user->photoSessions()->has('frame')->count() }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Permissions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Hak Akses</h3>
                <div class="space-y-2">
                    @if($user->hasRole('admin'))
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check mr-2"></i>
                            <span class="text-sm">Akses penuh admin</span>
                        </div>
                    @elseif($user->hasRole('manager'))
                        <div class="flex items-center text-blue-600">
                            <i class="fas fa-check mr-2"></i>
                            <span class="text-sm">Kelola sesi & customer</span>
                        </div>
                    @elseif($user->hasRole('operator'))
                        <div class="flex items-center text-purple-600">
                            <i class="fas fa-check mr-2"></i>
                            <span class="text-sm">Operasi photobox</span>
                        </div>
                    @else
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-camera mr-2"></i>
                            <span class="text-sm">Akses photobox saja</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions (for customers) -->
    @if($user->role === 'customer' && $user->photoSessions()->exists())
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Sesi Foto Terbaru</h3>
                     <a href="{{ route('admin.sessions.index', ['customer_id' => $user->id]) }}" 
                         class="link-brand text-sm font-medium">
                    Lihat Semua
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photobox</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->photoSessions()->latest()->limit(5)->with(['photobox', 'package'])->get() as $session)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->photobox->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $session->package->name ?? '-' }}
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
