<x-app-layout>
    <x-slot name="header">
        <h2 class="neon-text text-xl uppercase tracking-[0.2em] leading-tight">
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
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Description (Labeled as Notas) -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Notas / Apuntes')" />
                            <textarea id="description" name="description"
                                class="block mt-1 w-full bg-white/5 border-white/10 focus:border-cyan-500 focus:ring-cyan-500 rounded-xl shadow-sm text-white placeholder-white/20 backdrop-blur-md"
                                rows="4">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Auto Complete Toggle -->
                        <div class="mb-6 flex items-center gap-2">
                            <input type="checkbox" id="auto_complete" name="auto_complete" value="1" {{ old('auto_complete') ? 'checked' : '' }}
                                class="rounded border-white/10 bg-white/5 text-cyan-500 focus:ring-cyan-500">
                            <label for="auto_complete"
                                class="text-xs text-white/60 font-bold uppercase tracking-widest">Autocompletar al
                                llegar la hora</label>
                        </div>

                        <!-- Scheduled Date & Forecast -->
                        <div class="mb-8">
                            <x-custom-datetime-picker name="scheduled_at" :value="old('scheduled_at')" />
                            <p class="mt-4 text-[10px] text-white/30 uppercase tracking-[0.2em]">Elige una fecha para
                                visualizar el clima previsto en la línea de tiempo.</p>
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                        </div>

                        <!-- Map Selection -->
                        <div class="mb-6">
                            <x-input-label :value="__('Ubicación (Opcional - Haz click en el mapa)')" />
                            <div id="map"
                                class="h-[300px] w-full rounded-2xl border border-white/10 mt-2 z-0 shadow-2xl"></div>

                            <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                            <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('tasks.index') }}"
                                class="mr-4 text-gray-400 hover:text-cyan-400 transition-colors uppercase text-xs font-bold tracking-widest">Cancelar</a>
                            <x-primary-button>
                                {{ __('Guardar Tarea') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <script>
                        function initMap() {
                            // Definimos el centro inicial del mapa usando la ubicación del usuario o Madrid por defecto
                            const defaultLat = {{ auth()->user()->lat ?? 40.4168 }};
                            const defaultLng = {{ auth()->user()->lng ?? -3.7038 }};

                            // Inicializamos el objeto Map de Google en el contenedor #map
                            const map = new google.maps.Map(document.getElementById("map"), {
                                center: { lat: defaultLat, lng: defaultLng },
                                zoom: 13,
                                styles: [
                                    // Estilos personalizados para un look "Dark Mode" premium
                                    { "elementType": "geometry", "stylers": [{ "color": "#242f3e" }] },
                                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#746855" }] },
                                    { "elementType": "labels.text.stroke", "stylers": [{ "color": "#242f3e" }] },
                                    { "featureType": "administrative.locality", "elementType": "labels.text.fill", "stylers": [{ "color": "#d59563" }] },
                                    { "featureType": "poi", "elementType": "labels.text.fill", "stylers": [{ "color": "#d59563" }] },
                                    { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#263c3f" }] },
                                    { "featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{ "color": "#6b9a76" }] },
                                    { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#38414e" }] },
                                    { "featureType": "road", "elementType": "geometry.stroke", "stylers": [{ "color": "#212a37" }] },
                                    { "featureType": "road", "elementType": "labels.text.fill", "stylers": [{ "color": "#9ca5b3" }] },
                                    { "featureType": "road.highway", "elementType": "geometry", "stylers": [{ "color": "#746855" }] },
                                    { "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [{ "color": "#1f2835" }] },
                                    { "featureType": "road.highway", "elementType": "labels.text.fill", "stylers": [{ "color": "#f3d19c" }] },
                                    { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#17263c" }] },
                                    { "featureType": "water", "elementType": "labels.text.fill", "stylers": [{ "color": "#515c6d" }] },
                                    { "featureType": "water", "elementType": "labels.text.stroke", "stylers": [{ "color": "#17263c" }] }
                                ],
                                disableDefaultUI: true, // Quitamos controles estándar para una UI más limpia
                                zoomControl: true
                            });

                            let marker = null;

                            // Si ya hay coordenadas guardadas (por ejemplo al editar o tras un error de validación), ponemos el marcador
                            @if(old('lat') && old('lng'))
                                marker = new google.maps.Marker({
                                    position: { lat: {{ old('lat') }}, lng: {{ old('lng') }} },
                                    map: map
                                });
                            @endif

                            // EVENT LISTENER: Capturamos el click en el mapa para posicionar el marcador y guardar los datos
                            map.addListener("click", (e) => {
                                const pos = e.latLng;
                                if (marker) {
                                    marker.setPosition(pos); // Movemos el marcador existente
                                } else {
                                    marker = new google.maps.Marker({ // Creamos uno nuevo si no existe
                                        position: pos,
                                        map: map
                                    });
                                }
                                // Actualizamos los inputs ocultos del formulario con la lat/lng obtenida del evento
                                document.getElementById("lat").value = pos.lat();
                                document.getElementById("lng").value = pos.lng();
                            });
                        }

                        // Initialize map when page is ready
                        document.addEventListener('DOMContentLoaded', initMap);
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>