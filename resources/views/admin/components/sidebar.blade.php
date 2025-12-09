<div :class="{ 
        'translate-x-0 ease-out': mobileOpen, 
        '-translate-x-full ease-in': !mobileOpen,
        'lg:w-64': sidebarOpen,
        'lg:w-20': !sidebarOpen
    }"
    class="fixed inset-y-0 left-0 z-30 w-64 bg-navy transition-all duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex flex-col h-full shadow-xl">

    <!-- Logo -->
    <div
        class="flex items-center justify-center h-20 border-b border-white/20 shrink-0 overflow-hidden whitespace-nowrap px-2">
        <template x-if="sidebarOpen">
            <img src="{{ asset('logo-fotoku-landscape.png') }}" alt="FotoQu" class="h-8 transition-all duration-300" />
        </template>
        <template x-if="!sidebarOpen">
            <img src="{{ asset('logo-fotoku-favicon.png') }}" alt="FQ" class="h-8 w-8 transition-all duration-300" />
        </template>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.dashboard') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-chart-line w-5 text-center transition-all duration-300"
                :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Dashboard</span>
        </a>

        <a href="{{ route('admin.sessions.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.sessions.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-images w-5 text-center transition-all duration-300"
                :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Sesi Foto</span>
        </a>

        <a href="{{ route('admin.sessions.create') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.sessions.create') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-plus w-5 text-center transition-all duration-300"
                :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Sesi Baru</span>
        </a>

        <a href="{{ route('admin.events.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.events.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-calendar-alt w-5 text-center transition-all duration-300"
                :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Event Mode</span>
        </a>

        <div class="border-t border-white/20 my-4"></div>

        <a href="{{ route('admin.photoboxes.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.photoboxes.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-cube w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Photoboxes</span>
        </a>

        <a href="{{ route('admin.packages.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.packages.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-box-open w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Paket & Harga</span>
        </a>

        <a href="{{ route('admin.frame-templates.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.frame-templates.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-palette w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Template Frame</span>
        </a>

        <a href="{{ route('admin.reports.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.reports.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-chart-bar w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Laporan</span>
        </a>

        <div class="border-t border-white/20 my-4"></div>

        @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('admin.users.index') }}"
                class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.users.*') ? 'bg-white/30 shadow-lg' : '' }}"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <i class="fas fa-users w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
                <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Kelola Pengguna</span>
            </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager']))
            <a href="{{ route('admin.customers.index') }}"
                class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.customers.*') ? 'bg-white/30 shadow-lg' : '' }}"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <i class="fas fa-address-book w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
                <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Data Customer</span>
            </a>
        @endif

        <div class="border-t border-white/20 my-4"></div>

        <a href="{{ route('admin.settings.index') }}"
            class="flex items-center px-4 py-3 text-white rounded-xl transition-all duration-200 hover:bg-white/20 whitespace-nowrap {{ request()->routeIs('admin.settings.*') ? 'bg-white/30 shadow-lg' : '' }}"
            :class="sidebarOpen ? 'justify-start' : 'justify-center'">
            <i class="fas fa-cog w-5 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-3' : 'text-lg'"></i>
            <span class="font-medium transition-opacity duration-300" x-show="sidebarOpen">Pengaturan Sistem</span>
        </a>
    </nav>

    <!-- User Menu -->
    <div class="p-4 border-t border-white/20 shrink-0 overflow-hidden whitespace-nowrap">
        <div class="flex items-center mb-3 transition-all duration-300" :class="sidebarOpen ? 'space-x-3' : 'justify-center'">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg shrink-0">
                <i class="fas fa-user text-navy"></i>
            </div>
            <div class="flex-1 min-w-0" x-show="sidebarOpen">
                <p class="text-white font-medium text-sm truncate">{{ auth()->user()->name }}</p>
                <p class="text-white/70 text-xs truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center px-3 py-2 text-white rounded-lg transition-all duration-200 hover:bg-white/20 whitespace-nowrap"
                :class="sidebarOpen ? 'justify-start' : 'justify-center'">
                <i class="fas fa-sign-out-alt w-4 text-center transition-all duration-300" :class="sidebarOpen ? 'mr-2' : ''"></i>
                <span class="text-sm" x-show="sidebarOpen">Logout</span>
            </button>
        </form>
    </div>
</div>