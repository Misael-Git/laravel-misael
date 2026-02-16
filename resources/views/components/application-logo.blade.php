<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <!-- Abstract "L" / Checkmark Logo -->
    <defs>
        <linearGradient id="logo-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#00f2ff;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#7000ff;stop-opacity:1" />
        </linearGradient>
        <filter id="glow">
            <feGaussianBlur stdDeviation="2.5" result="coloredBlur"/>
            <feMerge>
                <feMergeNode in="coloredBlur"/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>
    <rect x="20" y="20" width="60" height="60" rx="15" fill="none" stroke="url(#logo-gradient)" stroke-width="4" filter="url(#glow)" />
    <path d="M40 50L48 58L65 42" stroke="url(#logo-gradient)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" fill="none" filter="url(#glow)" />
</svg>
