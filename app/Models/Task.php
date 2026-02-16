<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'is_completed', 'lat', 'lng', 'scheduled_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Relación inversa: Una tarea pertenece a un usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el pronóstico del tiempo para la tarea si tiene ubicación y fecha.
     */
    public function getForecast()
    {
        if (!$this->lat || !$this->lng || !$this->scheduled_at) {
            return null;
        }

        // Solo hay pronóstico para los próximos 5 días con la API gratuita
        $diff = now()->diffInDays($this->scheduled_at, false);
        if ($diff < 0 || $diff > 5) {
            return null;
        }

        return cache()->remember("task_forecast_{$this->id}", 3600, function () {
            try {
                $response = \Illuminate\Support\Facades\Http::get("https://api.openweathermap.org/data/2.5/forecast", [
                    'lat' => $this->lat,
                    'lon' => $this->lng,
                    'appid' => env('OPENWEATHER_API_KEY'),
                    'units' => 'metric',
                    'lang' => 'es'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $scheduledTime = $this->scheduled_at->timestamp;
                    
                    // Buscar la entrada más cercana en el tiempo
                    $closest = null;
                    $minDiff = PHP_INT_MAX;

                    foreach ($data['list'] as $forecast) {
                        $diff = abs($forecast['dt'] - $scheduledTime);
                        if ($diff < $minDiff) {
                            $minDiff = $diff;
                            $closest = $forecast;
                        }
                    }

                    return $closest;
                }
            } catch (\Exception $e) {
                return null;
            }
            return null;
        });
    }
}
