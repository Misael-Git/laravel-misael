<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
    @if($weather)
        <div class="p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 shadow-sm rounded-lg">
            <h3 class="font-bold">Estado Climático Actual</h3>
            <p>Temperatura: {{ $weather['main']['temp'] }}°C | {{ ucfirst($weather['weather'][0]['description']) }}</p>

            {{-- AVISO RESTRICTIVO: Si llueve (Rain), nieva (Snow) o hay tormenta (Thunderstorm) --}}
            @php 
                $estado = $weather['weather'][0]['main']; 
                $condicionesAdversas = ['Rain', 'Snow', 'Thunderstorm', 'Drizzle'];
            @endphp

            @if(in_array($estado, $condicionesAdversas))
                <div class="mt-4 p-4 bg-red-600 text-white font-bold rounded animate-pulse">
                    ⚠️ AVISO RESTRICTIVO: Las condiciones climáticas son adversas. Se recomienda limitar las actividades externas.
                </div>
            @endif
        </div>
    @else
        <div class="p-4 bg-yellow-100 text-yellow-700 rounded-lg">
            Guarda tu ubicación en el mapa para ver el clima actual.
        </div>
    @endif
</div>

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
                        <p class="mt-1 text-sm text-gray-600">Introduce tus coordenadas y dirección para obtener el pronóstico del tiempo.</p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
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
                            <x-input-label for="address" value="Dirección Postal" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" placeholder="Ej: Calle Gran Vía, Madrid" />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Guardar Ubicación</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
</x-app-layout>