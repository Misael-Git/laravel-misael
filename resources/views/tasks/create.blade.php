<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Tarea') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card">
                <div class="p-6">
                    
                    <form action="{{ route('tasks.store') }}" method="POST" id="task-form">
                        @csrf
                        
                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Título')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Descripción')" />
                            <textarea id="description" name="description" class="block mt-1 w-full bg-white/5 border-white/10 focus:border-cyan-500 focus:ring-cyan-500 rounded-xl shadow-sm text-white placeholder-white/20 backdrop-blur-md" rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Scheduled Date & Forecast -->
                        <div class="mb-6">
                            <x-input-label for="scheduled_at" :value="__('¿Cuándo? (Opcional)')" />
                            <x-text-input id="scheduled_at" class="block mt-1 w-full" type="datetime-local" name="scheduled_at" :value="old('scheduled_at')" />
                            <p class="mt-2 text-xs text-white/40 uppercase tracking-widest">Elige una fecha para ver el clima previsto.</p>
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                        </div>

                        <!-- Map Selection -->
                        <div class="mb-6">
                            <x-input-label :value="__('Ubicación (Opcional - Haz click en el mapa)')" />
                            <div id="map" class="h-[300px] w-full rounded-2xl border border-white/10 mt-2 z-0 shadow-2xl"></div>
                            
                            <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                            <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.index') }}" class="mr-4 text-gray-400 hover:text-cyan-400 transition-colors uppercase text-xs font-bold tracking-widest">Cancelar</a>
                            <x-primary-button>
                                {{ __('Guardar Tarea') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Default to user location if available, else a neutral point
                            let defaultLat = {{ auth()->user()->lat ?? 40.4168 }};
                            let defaultLng = {{ auth()->user()->lng ?? -3.7038 }};

                            const map = L.map('map').setView([defaultLat, defaultLng], 13);

                            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                                subdomains: 'abcd',
                                maxZoom: 20
                            }).addTo(map);

                            let marker = null;

                            // If old coordinates exist (validation error), place marker
                            @if(old('lat') && old('lng'))
                                marker = L.marker([{{ old('lat') }}, {{ old('lng') }}]).addTo(map);
                            @endif

                            map.on('click', function(e) {
                                if (marker) {
                                    marker.setLatLng(e.latlng);
                                } else {
                                    marker = L.marker(e.latlng).addTo(map);
                                }

                                document.getElementById('lat').value = e.latlng.lat;
                                document.getElementById('lng').value = e.latlng.lng;
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
