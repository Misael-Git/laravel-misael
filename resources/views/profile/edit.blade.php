<x-app-layout>
    <x-slot name="header">
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
                        <p class="mt-1 text-sm text-cyan-400/60 font-bold uppercase tracking-widest">Haz click en el
                            mapa para establecer tu ubicación predeterminada.</p>
                    </header>


                    <div class="mt-6 space-y-6">
                        <div id="profile-map"
                            class="h-[300px] w-full rounded-2xl border border-white/10 z-0 shadow-2xl"></div>

                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <x-input-label for="lat" value="Latitud" />
                                    <x-text-input id="lat" name="lat" type="text" class="mt-1 block w-full"
                                        :value="old('lat', $user->lat)" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('lat')" />
                                </div>


                                <div>
                                    <x-input-label for="lng" value="Longitud" />
                                    <x-text-input id="lng" name="lng" type="text" class="mt-1 block w-full"
                                        :value="old('lng', $user->lng)" readonly />
                                    <x-input-error class="mt-2" :messages="$errors->get('lng')" />
                                </div>
                            </div>


                            <div>
                                <x-input-label for="address" value="Dirección Postal o Referencia" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                    :value="old('address', $user->address)" placeholder="Ej: Mi Casa, Sevilla" />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button class="neon-button">Guardar Ubicación</button>
                            </div>
                        </form>
                    </div>

                    <script>
                        function initMap() {
                            const initLat = {{ $user->lat ?? 40.4168 }};
                            const initLng = {{ $user->lng ?? -3.7038 }};
                            const mapOptions = {
                                zoom: {{ $user->lat ? 13 : 5 }},
                                center: { lat: initLat, lng: initLng },
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

                            const map = new google.maps.Map(document.getElementById('profile-map'), mapOptions);
                            const geocoder = new google.maps.Geocoder();
                            let marker = null;

                            if ({{ $user->lat ? 'true' : 'false' }}) {
                                marker = new google.maps.Marker({
                                    position: { lat: initLat, lng: initLng },
                                    map: map
                                });
                            }

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

                                document.getElementById('lat').value = lat;
                                document.getElementById('lng').value = lng;

                                // Reverse Geocoding
                                geocoder.geocode({ location: e.latLng }, (results, status) => {
                                    if (status === "OK") {
                                        if (results[0]) {
                                            document.getElementById('address').value = results[0].formatted_address;
                                        }
                                    }
                                });
                            });
                        }

                        document.addEventListener('DOMContentLoaded', initMap);
                    </script>
                </div>
            </div>
</x-app-layout>