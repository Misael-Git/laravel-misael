<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cyan-400 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Mandatory Location Picker --}}
            @if(!auth()->user()->lat || !auth()->user()->lng)
                <div class="glass-card border-red-500/50">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center text-red-500 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white uppercase tracking-tighter">Establece tu ubicación</h3>
                                <p class="text-gray-400 text-sm">Necesitamos tu ubicación para mostrarte el clima y organizar tus tareas correctamente.</p>
                            </div>
                        </div>

                        <div id="mandatory-map" class="h-[400px] w-full rounded-2xl border border-white/10 z-0 shadow-2xl mb-6"></div>
                        
                        <form method="post" action="{{ route('profile.update') }}" class="flex items-center justify-between gap-4">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="lat" id="m-lat">
                            <input type="hidden" name="lng" id="m-lng">
                            <input type="hidden" name="name" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                            
                            <div class="text-cyan-400 text-sm font-mono tracking-widest hidden" id="coords-display">
                                SELECCIONADO: <span id="lat-val">0</span>, <span id="lng-val">0</span>
                            </div>
                            
                            <button class="neon-button ml-auto" id="save-loc-btn" disabled>Confirmar Ubicación</button>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const map = L.map('mandatory-map').setView([40.4168, -3.7038], 5);
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                            attribution: '&copy; CARTO',
                            subdomains: 'abcd',
                            maxZoom: 20
                        }).addTo(map);

                        let marker = null;
                        map.on('click', function(e) {
                            if (marker) marker.setLatLng(e.latlng);
                            else marker = L.marker(e.latlng).addTo(map);

                            document.getElementById('m-lat').value = e.latlng.lat;
                            document.getElementById('m-lng').value = e.latlng.lng;
                            document.getElementById('lat-val').textContent = e.latlng.lat.toFixed(4);
                            document.getElementById('lng-val').textContent = e.latlng.lng.toFixed(4);
                            document.getElementById('coords-display').classList.remove('hidden');
                            document.getElementById('save-loc-btn').disabled = false;
                        });
                    });
                </script>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Upcoming Events (Next 3) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="neon-text text-xl uppercase tracking-widest">Próximos Eventos</h3>
                        <a href="{{ route('tasks.index') }}" class="text-xs text-white/40 hover:text-cyan-400 transition-colors uppercase font-bold">Ver Todo</a>
                    </div>

                    @php
                        $upcomingTasks = auth()->user()->tasks()
                            ->where('scheduled_at', '>=', now())
                            ->orderBy('scheduled_at', 'asc')
                            ->take(3)
                            ->get();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($upcomingTasks as $task)
                            <div class="glass-card hover:scale-105">
                                <div class="text-[10px] text-cyan-400 font-bold uppercase mb-2">
                                    {{ $task->scheduled_at->diffForHumans() }}
                                </div>
                                <h4 class="text-white font-bold truncate">{{ $task->title }}</h4>
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="text-[10px] text-white/40">
                                        {{ $task->scheduled_at->format('H:i') }}
                                    </div>
                                    @php $forecast = $task->getForecast(); @endphp
                                    @if($forecast)
                                        <div class="flex items-center gap-1">
                                            <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png" class="w-6 h-6">
                                            <span class="text-xs text-cyan-400">{{ round($forecast['main']['temp']) }}°</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 glass-card text-center py-8 text-white/20 italic">
                                No tienes eventos próximos.
                            </div>
                        @endforelse
                    </div>

                    {{-- Timeline View --}}
                    <div class="mt-12" x-data="{ view: 'list' }">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="neon-text text-xl uppercase tracking-widest">Línea de Tiempo</h3>
                            <div class="flex bg-white/5 rounded-full p-1 border border-white/10">
                                <button @click="view = 'list'" :class="view === 'list' ? 'bg-cyan-500 text-white shadow-lg' : 'text-white/40 hover:text-white'" class="px-4 py-1 rounded-full text-xs font-bold uppercase transition-all">Lista</button>
                                <button @click="view = 'timeline'" :class="view === 'timeline' ? 'bg-cyan-500 text-white shadow-lg' : 'text-white/40 hover:text-white'" class="px-4 py-1 rounded-full text-xs font-bold uppercase transition-all">Timeline</button>
                            </div>
                        </div>

                        <div x-show="view === 'list'" class="space-y-4">
                            @foreach($upcomingTasks as $task)
                                <div class="glass-panel p-4 flex items-center justify-between hover:bg-white/10 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-400 font-bold text-xs">
                                            {{ $task->scheduled_at->format('d') }}
                                        </div>
                                        <div>
                                            <div class="text-white font-bold">{{ $task->title }}</div>
                                            <div class="text-[10px] text-white/40 uppercase">{{ $task->scheduled_at->format('F Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-cyan-400 font-bold">{{ $task->scheduled_at->format('H:i') }}</div>
                                        <div class="text-[10px] text-white/20">EVENTO</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div x-show="view === 'timeline'" class="timeline-container">
                            @foreach($upcomingTasks as $task)
                                <div class="relative pl-4">
                                    <div class="timeline-dot"></div>
                                    <div class="glass-card mb-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-xs text-cyan-400 font-bold uppercase mb-1">{{ $task->scheduled_at->format('l, d M Y') }}</div>
                                                <h4 class="text-lg font-bold text-white">{{ $task->title }}</h4>
                                                <p class="text-sm text-white/60 mt-2">{{ $task->description }}</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-white">{{ $task->scheduled_at->format('H:i') }}</div>
                                                @if($forecast = $task->getForecast())
                                                    <div class="flex items-center justify-end gap-2 mt-2">
                                                        <span class="text-xs text-white/40">{{ $forecast['weather'][0]['description'] }}</span>
                                                        <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png" class="w-8 h-8">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Side Info / Interactive Section --}}
                <div class="space-y-6">
                    <div class="glass-card overflow-hidden group">
                        <div class="h-32 bg-cyan-500/20 flex items-center justify-center relative overflow-hidden">
                            <div class="neon-text text-4xl uppercase tracking-[0.2em] relative z-10">LISTS</div>
                            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-cyan-400/20 blur-3xl group-hover:bg-cyan-400/40 transition-all duration-700"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-white font-bold mb-2">Bienvenido, {{ auth()->user()->name }}</h3>
                            <p class="text-sm text-white/40">Gestiona tus eventos y mantente al tanto del clima global.</p>
                            
                            <div class="mt-6 pt-6 border-t border-white/10 space-y-4 font-mono text-xs">
                                <div class="flex justify-between">
                                    <span class="text-white/40">TOTAL TAREAS</span>
                                    <span class="text-cyan-400">{{ auth()->user()->tasks()->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-white/40">PENDIENTES</span>
                                    <span class="text-cyan-400">{{ auth()->user()->tasks()->where('is_completed', false)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
