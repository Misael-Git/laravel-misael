<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$task = \App\Models\Task::latest()->first();
if (!$task) {
    echo "No tasks\n";
    exit;
}

echo "Task ID: " . $task->id . "\n";
echo "Task Lat/Lng: " . $task->lat . ", " . $task->lng . "\n";
echo "Task Scheduled: " . $task->scheduled_at . "\n";
echo "User Lat/Lng: " . ($task->user->lat ?? 'null') . ", " . ($task->user->lng ?? 'null') . "\n";

$lat = $task->lat ?: $task->user->lat;
$lng = $task->lng ?: $task->user->lng;

echo "Resolved Lat/Lng: " . $lat . ", " . $lng . "\n";

$forecast = $task->getForecast();

if ($forecast) {
    echo "Forecast time: " . gmdate("Y-m-d H:i:s", $forecast['dt']) . "\n";
    echo "Forecast temp: " . $forecast['main']['temp'] . "\n";
} else {
    echo "No forecast returned\n";

    // Debug why
    $diff = now()->diffInDays($task->scheduled_at, false);
    echo "Diff in days: $diff\n";
}
