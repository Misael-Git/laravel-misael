<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // El "View Composer" nos permite adjuntar datos a las vistas de forma global.
        // El '*' indica que se aplicará a TODAS las vistas de la aplicación.
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();

                // Usamos la Cache para no saturar la API de OpenWeather en cada carga de página.
                // 'remember' busca el valor en cache; si no existe, ejecuta el closure y guarda el resultado.
                // La clave es única por usuario ("weather_{user_id}") y dura 600 segundos (10 min).
                $weather = cache()->remember("weather_{$user->id}", 600, function () use ($user) {
                    if ($user->lat && $user->lng) {
                        // Http::withoutVerifying() se usa aquí para evitar problemas de certificados SSL locales.
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://api.openweathermap.org/data/2.5/weather", [
                            'lat' => $user->lat,
                            'lon' => $user->lng,
                            'appid' => env('OPENWEATHER_API_KEY'),
                            'units' => 'metric',
                            'lang' => 'es'
                        ]);

                        if ($response->successful()) {
                            return $response->json();
                        }
                    }
                    return null;
                });

                // Inyectamos la variable 'globalWeather' en todas las vistas.
                $view->with('globalWeather', $weather);
            }
        });
    }
}
