<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-cyan-400 leading-tight">
            {{ __('Lista de Tareas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card">
                <div class="p-6">
                    
                    @if(session('success'))
                        <div class="bg-green-500/10 border-l-4 border-green-500 text-green-400 p-4 mb-4 rounded-r-xl" role="alert">
                            <p class="font-bold uppercase tracking-widest text-xs">{{ session('success') }}</p>
                        </div>
                    @endif

                    @php
                        $isAdverse = false;
                        if (isset($globalWeather)) {
                            $estado = $globalWeather['weather'][0]['main'];
                            $condicionesAdversas = ['Rain', 'Snow', 'Thunderstorm', 'Drizzle'];
                            $isAdverse = in_array($estado, $condicionesAdversas);
                        }
                    @endphp

                    @if($isAdverse)
                        <div class="mb-4 p-4 bg-red-500/10 border-l-4 border-red-500 text-red-400 font-bold animate-pulse rounded-r-xl">
                            ⚠️ ATENCIÓN: Debido a condiciones climáticas adversas ({{ $globalWeather['weather'][0]['description'] }}), la creación de nuevas tareas está temporalmente deshabilitada por seguridad.
                        </div>
                    @endif

                    <div class="mb-4">
                        @if($isAdverse)
                            <button disabled class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                                {{ __('Crear Nueva Tarea (Bloqueado por clima)') }}
                            </button>
                        @else
                            <a href="{{ route('tasks.create') }}" class="neon-button">
                                {{ __('Crear Nueva Tarea') }}
                            </a>
                        @endif
                    </div>

                    <div class="relative overflow-x-auto glass-panel">
                        <table class="w-full text-sm text-left text-gray-300">
                            <thead class="text-xs text-cyan-400 uppercase bg-white/5">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center">Programada</th>
                                    <th scope="col" class="px-6 py-3 text-center">Clima</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    @php
                                        $forecast = $task->getForecast();
                                    @endphp
                                    <tr class="bg-transparent border-b border-white/5 hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 font-bold text-white whitespace-nowrap">
                                            {{ $task->title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ Str::limit($task->description, 50) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($task->scheduled_at)
                                                <div class="text-xs font-bold text-cyan-400 uppercase tracking-tighter">
                                                    {{ $task->scheduled_at->format('d M, Y') }}
                                                </div>
                                                <div class="text-[10px] text-white/40">
                                                    {{ $task->scheduled_at->format('H:i') }}
                                                </div>
                                            @else
                                                <span class="text-white/20">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($forecast)
                                                <div class="flex flex-col items-center">
                                                    <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png" alt="Weather" class="w-8 h-8">
                                                    <span class="text-xs font-bold text-white">{{ round($forecast['main']['temp']) }}°C</span>
                                                    <span class="text-[8px] text-cyan-400 uppercase tracking-widest text-center">{{ $forecast['weather'][0]['description'] }}</span>
                                                </div>
                                            @else
                                                <span class="text-white/20">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold uppercase tracking-widest rounded-full {{ $task->is_completed ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-500' }}">
                                                {{ $task->is_completed ? 'Completada' : 'Pendiente' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 flex space-x-2">
                                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-blue-600 hover:underline">Ver</a>
                                            <a href="{{ route('tasks.edit', $task) }}" class="font-medium text-blue-600 hover:underline">Editar</a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Estás seguro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="font-medium text-red-600 hover:underline">Borrar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center">No hay tareas registradas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
