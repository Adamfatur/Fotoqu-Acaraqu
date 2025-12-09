@extends('admin.layout')

@section('header', 'Manajemen User')
@section('description', 'Kelola admin, manager, operator, dan customer')

@section('content')
    <div x-data="userActionModal()" class="space-y-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-orange-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Admin</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ App\Models\User::where('role', 'admin')->count() }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-600">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-blue-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Manager</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::where('role', 'manager')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-users-cog text-xl"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-teal-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Operator</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::where('role', 'operator')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center text-teal-600">
                    <i class="fas fa-user-cog text-xl"></i>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl shadow-sm border border-purple-100 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Customer</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">
                        {{ App\Models\User::where('role', 'customer')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters & Actions -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-gray-800">Daftar Pengguna</h2>

                <form method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-grow sm:flex-grow-0 sm:w-64">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user..."
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>

                    <select name="role"
                        class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none cursor-pointer">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2.5 bg-gray-800 text-white rounded-xl hover:bg-gray-700 transition-colors shadow-sm">
                            <i class="fas fa-filter"></i>
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}"
                                class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors"
                                title="Reset Filter">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                        <a href="{{ route('admin.users.create') }}"
                            class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-xl shadow-md hover:bg-blue-700 transition-all flex items-center justify-center whitespace-nowrap">
                            <i class="fas fa-plus mr-2"></i> User Baru
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users List/Grid -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr
                            class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Status & Role</th>
                            <th class="px-6 py-4">Last Login</th>
                            <th class="px-6 py-4">Terdaftar</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">
                                                {{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role_badge_color }}">
                                            {{ $user->role_name }}
                                        </span>
                                        <span
                                            class="inline-flex items-center text-xs font-medium {{ $user->status_badge_text_color }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->status_dot_color }}"></span>
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->last_login_at)
                                        <div class="text-sm text-gray-900">{{ $user->last_login_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->last_login_at->format('H:i') }}</div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum login</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-colors"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="p-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-blue-50 hover:text-teal-600 transition-colors"
                                            title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($user->isBanned())
                                            <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline"
                                                @submit.prevent="openModal('unban', {{ $user->id }}, '{{ e($user->name) }}', $el)">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition-colors"
                                                    title="Unban">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            </form>
                                        @else
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.ban', $user) }}" class="inline"
                                                    @submit.prevent="openModal('ban', {{ $user->id }}, '{{ e($user->name) }}', $el)">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition-colors"
                                                        title="Ban">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                                @submit.prevent="openModal('delete', {{ $user->id }}, '{{ e($user->name) }}', $el)">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-users-slash text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-gray-900 font-medium mb-1">Tidak ada user ditemukan</h3>
                                        <p class="text-gray-500 text-sm">Coba ubah filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Enhanced Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true" x-cloak>
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" @click="closeModal()"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                :class="{
                                    'bg-red-100': modalType === 'delete',
                                    'bg-orange-100': modalType === 'ban',
                                    'bg-green-100': modalType === 'unban'
                                 }">
                                <i class="fas text-lg" :class="{
                                       'fa-trash-alt text-red-600': modalType === 'delete',
                                       'fa-ban text-orange-600': modalType === 'ban',
                                       'fa-unlock text-green-600': modalType === 'unban'
                                   }"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    <span x-show="modalType === 'delete'">Hapus User</span>
                                    <span x-show="modalType === 'ban'">Blokir User</span>
                                    <span x-show="modalType === 'unban'">Aktifkan User</span>
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        <span x-show="modalType === 'delete'">Apakah Anda yakin ingin menghapus user <strong
                                                x-text="modalName"></strong>? Tindakan ini tidak dapat dibatalkan.</span>
                                        <span x-show="modalType === 'ban'">User <strong x-text="modalName"></strong> tidak
                                            akan bisa login ke sistem jika diblokir.</span>
                                        <span x-show="modalType === 'unban'">User <strong x-text="modalName"></strong> akan
                                            dapat mengakses sistem kembali.</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="proceedAction()" type="button"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                            :class="{
                                    'bg-red-600 hover:bg-red-700 focus:ring-red-500': modalType === 'delete',
                                    'bg-orange-600 hover:bg-orange-700 focus:ring-orange-500': modalType === 'ban',
                                    'bg-green-600 hover:bg-green-700 focus:ring-green-500': modalType === 'unban'
                                }">
                            <span x-show="modalType === 'delete'">Ya, Hapus</span>
                            <span x-show="modalType === 'ban'">Blokir</span>
                            <span x-show="modalType === 'unban'">Aktifkan</span>
                        </button>
                        <button @click="closeModal()" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function userActionModal() {
                return {
                    showModal: false,
                    modalType: '', // 'delete', 'ban', 'unban'
                    modalId: null,
                    modalName: '',
                    modalForm: null,

                    openModal(type, id, name, el) {
                        this.modalType = type;
                        this.modalId = id;
                        this.modalName = name;
                        this.modalForm = el.closest('form');
                        this.showModal = true;
                    },

                    closeModal() {
                        this.showModal = false;
                        // Wait for transition
                        setTimeout(() => {
                            this.modalType = '';
                            this.modalId = null;
                            this.modalName = '';
                            this.modalForm = null;
                        }, 300);
                    },

                    proceedAction() {
                        if (this.modalForm) {
                            this.modalForm.submit();
                            this.showModal = false;
                        }
                    }
                }
            }
        </script>
    </div>
@endsection