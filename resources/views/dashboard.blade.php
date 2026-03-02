<x-app-layout>
    <x-slot name="header">
        <h2 class="neon-text text-xl uppercase tracking-[0.2em] leading-tight">
            {{ __('De un vistazo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Mandatory Location Picker --}}
            @if(!auth()->user()->lat || !auth()->user()->lng)
                <div class="glass-card border-red-500/50">
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div
                                class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center text-red-500 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white uppercase tracking-tighter">Establece tu ubicación
                                </h3>
                                <p class="text-gray-400 text-sm">Necesitamos tu ubicación para mostrarte el clima y
                                    organizar tus tareas correctamente.</p>
                            </div>
                        </div>

                        <div id="mandatory-map"
                            class="h-[400px] w-full rounded-2xl border border-white/10 z-0 shadow-2xl mb-6"></div>

                        <form method="post" action="{{ route('profile.update') }}"
                            class="flex items-center justify-between gap-4">
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
                    function initMandatoryMap() {
                        const defaultCenter = { lat: 40.4168, lng: -3.7038 };
                        const mapOptions = {
                            zoom: 5,
                            center: defaultCenter,
                            styles: [
                                { "elementType": "geometry", "stylers": [{ "color": "#242f3e" }] },
                                { "elementType": "labels.text.fill", "stylers": [{ "color": "#746855" }] },
                                { "elementType": "labels.text.stroke", "stylers": [{ "color": "#242f3e" }] },
                                { "featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [{ "color": "#d59563" }] },
                                { "featureType": "poi", "elementType": "labels.text.fill", "stylers": [{ "color": "#d59563" }] },
                                { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#263c3f" }] },
                                { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#38414e" }] },
                                { "featureType": "road", "elementType": "geometry.stroke", "stylers": [{ "color": "#212a37" }] },
                                { "featureType": "road", "elementType": "labels.text.fill", "stylers": [{ "color": "#9ca5b3" }] },
                                { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#17263c" }] }
                            ]
                        };

                        const map = new google.maps.Map(document.getElementById('mandatory-map'), mapOptions);
                        const geocoder = new google.maps.Geocoder();
                        let marker = null;

                        map.addListener('click', (e) => {
                            const lat = e.latLng.lat();
                            const lng = e.latLng.lng();

                            if (marker) {
                                marker.setPosition(e.latLng);
                            } else {
                                marker = new google.maps.Marker({
                                    position: e.latLng,
                                    map: map
                                });
                            }

                            document.getElementById('m-lat').value = lat;
                            document.getElementById('m-lng').value = lng;
                            document.getElementById('lat-val').textContent = lat.toFixed(4);
                            document.getElementById('lng-val').textContent = lng.toFixed(4);
                            document.getElementById('coords-display').classList.remove('hidden');
                            document.getElementById('save-loc-btn').disabled = false;
                        });
                    }

                    document.addEventListener('DOMContentLoaded', initMandatoryMap);
                </script>
            @endif

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Left Column: Timeline --}}
                <div class="lg:w-2/3">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="neon-text text-2xl uppercase tracking-[0.2em] flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-cyan-500 rounded-full shadow-[0_0_10px_#00f2ff]"></span>
                            Línea de Tiempo
                        </h3>
                        <a href="{{ route('tasks.index') }}"
                            class="text-[10px] text-white/40 hover:text-cyan-400 font-bold uppercase tracking-widest transition-colors">Ver
                            Todas →</a>
                    </div>

                    <div class="timeline-container relative">
                        <div class="absolute left-[7px] top-2 bottom-2 w-px bg-white/10"></div>

                        @php
                            $upcomingTasks = Auth::user()->tasks()
                                ->where('scheduled_at', '>=', now())
                                ->orderBy('scheduled_at', 'asc')
                                ->get();
                        @endphp

                        @forelse ($upcomingTasks as $task)
                            @php $forecast = $task->getForecast(); @endphp
                            <div class="relative pl-8 mb-6 group" x-data="{ showNotes: false, saving: false }">
                                <div
                                    class="absolute left-0 top-2 w-4 h-4 rounded-full border-2 border-cyan-500 bg-[#0a0a12] shadow-[0_0_8px_#00f2ff] z-10 transition-transform group-hover:scale-125">
                                </div>

                                <div class="glass-card hover:border-cyan-500/50 transition-all cursor-pointer"
                                    @click="showNotes = !showNotes">
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                        <div class="flex-grow">
                                            <div class="flex items-center gap-3 mb-1">
                                                <span
                                                    class="text-[10px] text-cyan-400 font-black uppercase tracking-widest">
                                                    {{ $task->scheduled_at->format('l, d M') }}
                                                </span>
                                                @if($task->is_completed)
                                                    <span
                                                        class="px-2 py-0.5 rounded-full bg-green-500/20 text-green-400 text-[8px] font-black uppercase">Hecho</span>
                                                @endif

                                                @if($forecast)
                                                    <div
                                                        class="flex items-center gap-1.5 px-2 py-0.5 bg-white/5 rounded-md border border-white/5">
                                                        <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png"
                                                            class="w-4 h-4" alt="weather">
                                                        <span
                                                            class="text-[8px] text-white/60 font-bold uppercase">{{ round($forecast['main']['temp']) }}°C</span>
                                                        @if($task->isWeatherAdverse())
                                                            <span
                                                                class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse ml-1"></span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            <h4
                                                class="text-xl font-bold text-white transition-colors {{ $task->is_completed ? 'line-through opacity-40' : '' }}">
                                                {{ $task->title }}
                                            </h4>
                                        </div>

                                        <div class="flex items-center gap-6">
                                            <div class="text-right">
                                                <div class="text-2xl font-black text-white tracking-widest">
                                                    {{ $task->scheduled_at->format('H:i') }}
                                                </div>
                                                <div class="text-[8px] text-white/20 uppercase font-bold">
                                                    {{ $task->scheduled_at->diffForHumans() }}
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2" @click.stop>
                                                <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="p-2 bg-white/5 hover:bg-green-500/20 rounded-xl transition-all {{ $task->is_completed ? 'text-green-500' : 'text-white/20 hover:text-green-400' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notes Interaction --}}
                                    <div x-show="showNotes" x-transition class="mt-4 pt-4 border-t border-white/10"
                                        @click.stop>
                                        <div class="flex items-center justify-between mb-2">
                                            <label
                                                class="text-[8px] text-white/30 uppercase font-black tracking-widest">Notas
                                                / Apuntes</label>
                                            <a href="{{ route('tasks.edit', $task) }}"
                                                class="text-white/20 hover:text-cyan-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        </div>
                                        <textarea x-ref="noteText"
                                            class="w-full bg-white/5 border-white/10 rounded-xl text-xs text-white p-3 min-h-[100px] focus:border-cyan-500 focus:ring-0 transition-all"
                                            placeholder="Escribe algo...">{{ $task->description }}</textarea>
                                        <div class="flex justify-end mt-2 items-center gap-3">
                                            <span x-show="saving"
                                                class="text-[8px] text-cyan-400 animate-pulse font-bold uppercase tracking-widest">Guardando...</span>
                                            <button @click="
                                                                    saving = true;
                                                                    fetch('{{ route('tasks.update', $task) }}', {
                                                                        method: 'PATCH',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                            'Accept': 'application/json'
                                                                        },
                                                                        body: JSON.stringify({ 
                                                                            description: $refs.noteText.value,
                                                                            title: '{{ $task->title }}'
                                                                        })
                                                                    })
                                                                    .then(response => response.json())
                                                                    .then(data => {
                                                                        saving = false;
                                                                    })
                                                                    .catch(error => {
                                                                        console.error('Error:', error);
                                                                        saving = false;
                                                                    })
                                                                "
                                                class="text-[10px] text-cyan-400 font-bold uppercase tracking-widest hover:text-cyan-300 transition-colors">
                                                Guardar Nota
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="glass-card text-center py-20">
                                <span class="text-white/10 uppercase font-black tracking-[0.5em] italic">No hay eventos
                                    próximos</span>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right Column: Past Tasks --}}
                <div class="lg:w-1/3">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-white/60 text-lg font-black uppercase tracking-widest flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/20" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Tareas Pasadas
                        </h3>
                        <a href="{{ route('tasks.index') }}"
                            class="text-[8px] text-white/20 hover:text-cyan-400 font-black uppercase tracking-widest">Ver
                            Todas →</a>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $pastTasks = Auth::user()->tasks()
                                ->where('scheduled_at', '<', now())
                                ->orderBy('is_completed', 'asc')
                                ->orderBy('scheduled_at', 'desc')
                                ->take(6)
                                ->get();
                        @endphp

                        @foreach ($pastTasks as $task)
                            @php $forecast = $task->getForecast(); @endphp
                            <div
                                class="glass-card p-3 relative group overflow-hidden {{ !$task->is_completed ? 'border-red-500/20 shadow-[inset_0_0_20px_rgba(239,68,68,0.05)]' : '' }}">
                                @if(!$task->is_completed)
                                    <div class="absolute top-0 right-0 w-1 h-full bg-red-500 animate-pulse"></div>
                                @endif

                                <div class="flex flex-col h-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span
                                            class="text-[7px] font-black uppercase tracking-widest {{ !$task->is_completed ? 'text-red-400' : 'text-white/20' }}">
                                            {{ $task->scheduled_at->diffForHumans() }}
                                        </span>
                                        @if($forecast)
                                            <div
                                                class="flex items-center gap-1 opacity-50 group-hover:opacity-100 transition-opacity">
                                                <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png"
                                                    class="w-3 h-3" alt="w">
                                                <span
                                                    class="text-[7px] text-white font-bold">{{ round($forecast['main']['temp']) }}°</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h5
                                        class="text-xs font-bold text-white mt-1 mb-2 line-clamp-2 {{ $task->is_completed ? 'opacity-40 line-through' : '' }}">
                                        {{ $task->title }}
                                    </h5>

                                    <div class="mt-auto flex items-center justify-between pt-2 border-t border-white/5">
                                        <div class="text-[8px] font-bold text-white/40">
                                            {{ $task->scheduled_at->format('d M') }}
                                        </div>
                                        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 transition-all {{ $task->is_completed ? 'text-green-500' : 'text-white/20' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($pastTasks->isEmpty())
                        <div class="border border-dashed border-white/5 rounded-2xl py-10 text-center">
                            <span class="text-[8px] text-white/10 uppercase font-black tracking-widest">Todo al día</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>