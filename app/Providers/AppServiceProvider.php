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
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $weather = cache()->remember("weather_{$user->id}", 600, function () use ($user) {
                    if ($user->lat && $user->lng) {
                        $response = \Illuminate\Support\Facades\Http::get("https://api.openweathermap.org/data/2.5/weather", [
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

                $view->with('globalWeather', $weather);
            }
        });
    }
}
