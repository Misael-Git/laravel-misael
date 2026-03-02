<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lists - Premium Task Management</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="antialiased text-white overflow-hidden">
    <!-- Mesh Gradient Background -->
    <div class="mesh-bg"></div>

    <div class="relative z-10 min-h-screen flex flex-col">
        <!-- Navigation -->
        <header class="p-6">
            <nav class="max-w-7xl mx-auto flex justify-between items-center glass-panel px-8 py-4 rounded-3xl">
                <div class="flex items-center gap-3">
                    <x-application-logo class="w-10 h-10 fill-current text-cyan-400" />
                    <span class="text-2xl font-extrabold tracking-tighter neon-text">LISTS</span>
                </div>

                <div class="flex items-center gap-6">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold hover:text-cyan-400 transition-colors">Panel</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold hover:text-cyan-400 transition-colors uppercase tracking-widest">Iniciar
                                Sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="neon-button px-6 py-2 rounded-xl text-sm font-bold uppercase tracking-widest">Registrarse</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center px-6">
            <div class="max-w-4xl w-full text-center space-y-8 animate-fade-in-up">
                <div
                    class="inline-block glass-card px-4 py-1 rounded-full text-xs font-bold text-cyan-400 uppercase tracking-widest mb-4">
                    El Futuro de la Productividad
                </div>
                <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-none">
                    Organiza tu vida <br />
                    <span class="neon-text">con claridad cristalina.</span>
                </h1>
                <p class="text-xl text-white/60 max-w-2xl mx-auto font-light leading-relaxed">
                    Una experiencia premium de gestión de tareas construida con Glassmorphism y tecnología moderna.
                    Sigue tus metas, gestiona tareas y mantente a la vanguardia con Lists.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8">
                    <a href="{{ route('register') }}"
                        class="neon-button px-10 py-5 rounded-2xl text-lg font-black uppercase tracking-widest w-full sm:w-auto">
                        Comienza Gratis
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="p-8 text-center text-white/30 text-xs uppercase tracking-widest">
            &copy; {{ date('Y') }} Lists Inc. Creado con pasión y Glassmorphism.
        </footer>
    </div>
</body>

</html>