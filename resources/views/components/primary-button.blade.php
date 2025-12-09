<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-3 rounded-xl font-semibold text-sm text-slate-900 bg-[var(--carrot-orange)] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-[var(--carrot-orange)] focus:ring-offset-2 transition ease-in-out duration-200 shadow']) }}>
    {{ $slot }}
</button>
