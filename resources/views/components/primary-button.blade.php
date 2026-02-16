<button {{ $attributes->merge(['type' => 'submit', 'class' => 'neon-button inline-flex items-center px-6 py-2']) }}>
    {{ $slot }}
</button>
