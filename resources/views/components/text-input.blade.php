@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border-white/10 focus:border-cyan-500 focus:ring-cyan-500 rounded-xl shadow-sm text-white placeholder-white/20 backdrop-blur-md w-full']) }}>
