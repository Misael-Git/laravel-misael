<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="glass-card mb-6">
            @if($weather)
                <div class="p-4 bg-cyan-500/10 border-l-4 border-cyan-500 text-cyan-100 shadow-sm rounded-lg">
                    <h3 class="neon-text text-sm">Estado Climático Actual</h3>
                    <p class="text-xl">Temperatura: {{ $weather['main']['temp'] }}°C | <span class="text-cyan-400/60 capitalize">{{ $weather['weather'][0]['description'] }}</span></p>

                    @php 
                        $estado = $weather['weather'][0]['main']; 
                        $condicionesAdversas = ['Rain', 'Snow', 'Thunderstorm', 'Drizzle'];
                    @endphp

                    @if(in_array($estado, $condicionesAdversas))
                        <div class="mt-4 p-4 bg-red-600/20 border border-red-600 text-red-400 font-bold rounded animate-pulse">
                            ⚠️ AVISO RESTRICTIVO: Condiciones climáticas adversas detectadas.
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 bg-yellow-500/10 text-yellow-200 rounded-lg text-sm italic">
                    Guarda tu ubicación para habilitar el reporte climático.
                </div>
            @endif
        </div>
</div>

        <h2 class="font-semibold text-xl text-cyan-400 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 glass-card">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 glass-card">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-bold text-white uppercase tracking-tight">Ubicación Geográfica</h2>
                        <p class="mt-1 text-sm text-cyan-400/60 font-bold uppercase tracking-widest">Haz click en el mapa para establecer tu ubicación predeterminada.</p>
                    </header>

                    <div class="mt-6 space-y-6">
                        <div id="profile-map" class="h-[300px] w-full rounded-2xl border border-white/10 z-0 shadow-2xl"></div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="lat" value="Latitud" />
                                    <x-text-input id="lat" name="lat" type="text" class="mt-1 block w-full" :value="old('lat', $user->lat)" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('lat')" />
                                </div>

                                <div>
                                    <x-input-label for="lng" value="Longitud" />
                                    <x-text-input id="lng" name="lng" type="text" class="mt-1 block w-full" :value="old('lng', $user->lng)" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('lng')" />
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="address" value="Dirección Postal o Referencia" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" placeholder="Ej: Mi Casa, Sevilla" />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button class="neon-button">Guardar Ubicación</button>
                            </div>
                        </form>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let initLat = {{ $user->lat ?? 40.4168 }};
                            let initLng = {{ $user->lng ?? -3.7038 }};
                            const map = L.map('profile-map').setView([initLat, initLng], {{ $user->lat ? 13 : 5 }});

                            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                                attribution: '&copy; CARTO',
                                subdomains: 'abcd',
                                maxZoom: 20
                            }).addTo(map);

                            let marker = null;
                            if ({{ $user->lat ? 'true' : 'false' }}) {
                                marker = L.marker([initLat, initLng]).addTo(map);
                            }

                            map.on('click', function(e) {
                                if (marker) marker.setLatLng(e.latlng);
                                else marker = L.marker(e.latlng).addTo(map);

                                document.getElementById('lat').value = e.latlng.lat;
                                document.getElementById('lng').value = e.latlng.lng;
                            });
                        });
                    </script>
                </div>
            </div>
</x-app-layout>