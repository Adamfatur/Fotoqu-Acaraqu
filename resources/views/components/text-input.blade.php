@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-[#1a90d6] focus:ring-[#1a90d6] rounded-lg shadow-sm']) }}>
