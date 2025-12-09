<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email address</label>
            <div class="mt-2">
                <input id="email" name="email" type="email" autocomplete="email" required autofocus
                    class="block w-full rounded-xl border-0 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-[#1a90d6] sm:text-sm sm:leading-6 transition-all duration-200"
                    placeholder="nama@email.com" value="{{ old('email') }}">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
            </div>
            <div class="mt-2">
                <input id="password" name="password" type="password" autocomplete="current-password" required
                    class="block w-full rounded-xl border-0 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-[#1a90d6] sm:text-sm sm:leading-6 transition-all duration-200"
                    placeholder="••••••••">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember_me" name="remember" type="checkbox"
                    class="h-4 w-4 rounded border-slate-300 text-[#1a90d6] focus:ring-[#1a90d6]">
                <label for="remember_me" class="ml-3 block text-sm leading-6 text-slate-700">Ingat saya</label>
            </div>

            @if (Route::has('password.request'))
                <div class="text-sm leading-6">
                    <a href="{{ route('password.request') }}"
                        class="font-semibold text-[#1a90d6] hover:text-[#157bb7] transition-colors">
                        Lupa password?
                    </a>
                </div>
            @endif
        </div>

        <div>
            <button type="submit"
                class="flex w-full justify-center rounded-xl bg-[#1a90d6] px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-[#157bb7] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#1a90d6] transition-all duration-200 transform active:scale-95">
                Masuk Sekarang
            </button>
        </div>
    </form </x-guest-layout>