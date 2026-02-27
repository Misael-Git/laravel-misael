<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'notes', 'is_completed', 'auto_complete', 'lat', 'lng', 'scheduled_at'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * El "booted" method del modelo.
     */
    protected static function booted()
    {
        static::saving(function ($task) {
            if ($task->auto_complete && $task->scheduled_at && $task->scheduled_at->isPast()) {
                $task->is_completed = true;
            }
        });
    }

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
        $lat = $this->lat ?: $this->user->lat;
        $lng = $this->lng ?: $this->user->lng;

        if (!$lat || !$lng || !$this->scheduled_at) {
            return null;
        }

        // Solo hay pronóstico para los próximos 5 días con la API gratuita
        $diff = now()->diffInDays($this->scheduled_at, false);
        if ($diff < 0 || $diff > 5) {
            return null;
        }

        return cache()->remember("task_forecast_{$this->id}", 3600, function () {
            try {
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->get("https://api.openweathermap.org/data/2.5/forecast", [
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

    /**
     * Determina si el clima previsto para la tarea es adverso.
     */
    public function isWeatherAdverse()
    {
        $forecast = $this->getForecast();
        if (!$forecast) {
            return false;
        }

        $main = $forecast['weather'][0]['main'];
        $adverseConditions = ['Rain', 'Snow', 'Thunderstorm', 'Drizzle'];

        return in_array($main, $adverseConditions);
    }
}
