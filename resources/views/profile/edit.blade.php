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
                        <p class="mt-1 text-sm text-cyan-400/60">Busca tu dirección o introduce las coordenadas manualmente.</p>
                    </header>

                    <div class="mt-6 space-y-4">
                        <div>
                            <x-input-label for="address_search" value="Buscar Dirección (Geoapify)" />
                            <div class="flex gap-2">
                                <x-text-input id="address_search" type="text" class="flex-grow" placeholder="Ej: Calle Gran Vía, Madrid" />
                                <button type="button" onclick="searchAddress()" class="neon-button px-4 py-2 text-xs">Buscar</button>
                            </div>
                        </div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="lat" value="Latitud" />
                                    <x-text-input id="lat" name="lat" type="text" class="mt-1 block w-full" :value="old('lat', $user->lat)" placeholder="Ej: 40.4168" />
                                    <x-input-error class="mt-2" :messages="$errors->get('lat')" />
                                </div>

                                <div>
                                    <x-input-label for="lng" value="Longitud" />
                                    <x-text-input id="lng" name="lng" type="text" class="mt-1 block w-full" :value="old('lng', $user->lng)" placeholder="Ej: -3.7038" />
                                    <x-input-error class="mt-2" :messages="$errors->get('lng')" />
                                </div>
                            </div>
                            
                            <div>
                                <x-input-label for="address" value="Dirección Postal Confirmada" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" placeholder="Dirección autocompletada..." />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button class="neon-button">Guardar Ubicación</button>
                            </div>
                        </form>
                    </div>

                    <script>
                        function searchAddress() {
                            const query = document.getElementById('address_search').value;
                            if (!query) return;

                            const apiKey = "07dba4724dc64d5480388edfecb63e40";
                            const url = `https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(query)}&apiKey=${apiKey}`;

                            fetch(url)
                                .then(response => response.json())
                                .then(result => {
                                    if (result.features && result.features.length > 0) {
                                        const feature = result.features[0];
                                        document.getElementById('lat').value = feature.properties.lat;
                                        document.getElementById('lng').value = feature.properties.lon;
                                        document.getElementById('address').value = feature.properties.formatted;
                                    } else {
                                        alert("No se encontró la dirección.");
                                    }
                                })
                                .catch(error => console.log('error', error));
                        }
                    </script>
                </div>
            </div>
</x-app-layout>