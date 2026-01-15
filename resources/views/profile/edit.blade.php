<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Ubicación Geográfica</h2>
                        <p class="mt-1 text-sm text-gray-600">Arrastra el marcador en el mapa para actualizar tu ubicación.</p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div id="map" style="height: 300px; width: 100%; border-radius: 0.5rem;" class="mb-4"></div>

                        <input type="hidden" name="lat" id="lat" value="{{ old('lat', $user->lat) }}">
                        <input type="hidden" name="lng" id="lng" value="{{ old('lng', $user->lng) }}">
                        
                        <div>
                            <x-input-label for="address" value="Dirección Postal" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full bg-gray-100" :value="old('address', $user->address)" readonly />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Guardar Ubicación</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

<script>
        let map, marker, geocoder;

        function initMap() {
            const initialPos = { 
                lat: {{ $user->lat ?? 40.4167 }}, 
                lng: {{ $user->lng ?? -3.7033 }} 
            };

            geocoder = new google.maps.Geocoder();
            map = new google.maps.Map(document.getElementById("map"), {
                center: initialPos,
                zoom: 13,
            });

            marker = new google.maps.Marker({
                position: initialPos,
                map: map,
                draggable: true,
            });

            marker.addListener("dragend", () => {
                const pos = marker.getPosition();
                document.getElementById("lat").value = pos.lat();
                document.getElementById("lng").value = pos.lng();
                
                geocoder.geocode({ location: pos }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        document.getElementById("address").value = results[0].formatted_address;
                    }
                });
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>
</x-app-layout>