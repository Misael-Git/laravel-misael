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
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h3 class="neon-text text-2xl uppercase tracking-[0.2em]">Gestión de Tareas</h3>
                    <p class="text-white/40 text-xs mt-1 uppercase font-bold tracking-widest">Organiza tus eventos y visualiza el clima</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex bg-white/5 rounded-full p-1 border border-white/10">
                        <button @click="view = 'list'" :class="view === 'list' ? 'bg-cyan-500 text-white shadow-lg' : 'text-white/40 hover:text-white'" class="px-4 py-1 rounded-full text-xs font-bold uppercase transition-all">Lista</button>
                        <button @click="view = 'timeline'" :class="view === 'timeline' ? 'bg-cyan-500 text-white shadow-lg' : 'text-white/40 hover:text-white'" class="px-4 py-1 rounded-full text-xs font-bold uppercase transition-all">Timeline</button>
                    </div>
                    <a href="{{ route('tasks.create') }}" class="neon-button text-xs py-2 px-6">
                        + Nueva Tarea
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 glass-panel border-cyan-500/50 text-cyan-400 font-bold uppercase text-xs tracking-widest animate-pulse">
                    {{ session('success') }}
                </div>
            @endif

            {{-- List View --}}
            <div x-show="view === 'list'" class="glass-card !p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/10">
                                <th class="px-6 py-4 text-[10px] font-bold text-cyan-400 uppercase tracking-widest">Tarea</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-cyan-400 uppercase tracking-widest">Programación</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-cyan-400 uppercase tracking-widest text-center">Clima</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-cyan-400 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($tasks as $task)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full {{ $task->is_completed ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-cyan-500 shadow-[0_0_8px_rgba(6,182,212,0.6)] animate-pulse' }}"></div>
                                            <div>
                                                <div class="text-white font-bold group-hover:text-cyan-400 transition-colors {{ $task->is_completed ? 'line-through opacity-40' : '' }}">
                                                    {{ $task->title }}
                                                </div>
                                                @if($task->description)
                                                    <div class="text-[10px] text-white/30 truncate max-w-xs">{{ $task->description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($task->scheduled_at)
                                            <div class="text-xs text-white uppercase font-bold">{{ $task->scheduled_at->format('d M, Y') }}</div>
                                            <div class="text-[10px] text-cyan-400/60 font-mono">{{ $task->scheduled_at->format('H:i') }} ({{ $task->scheduled_at->diffForHumans() }})</div>
                                        @else
                                            <span class="text-[10px] text-white/20 italic">No programada</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $forecast = $task->getForecast(); @endphp
                                        @if($forecast)
                                            <div class="flex flex-col items-center group/weather">
                                                <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}@2x.png" class="w-10 h-10 -my-2" title="{{ $forecast['weather'][0]['description'] }}">
                                                <span class="text-[10px] font-bold text-cyan-400 tracking-tighter">{{ round($forecast['main']['temp']) }}°C</span>
                                            </div>
                                        @else
                                            <div class="text-center text-[10px] text-white/10 uppercase italic">N/A</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-white/40 hover:text-cyan-400 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('¿Eliminar esta tarea?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-white/40 hover:text-red-500 transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-white/20 italic uppercase tracking-widest text-sm">
                                        No has creado ninguna tarea todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Timeline View --}}
            <div x-show="view === 'timeline'" class="timeline-container">
                @forelse ($tasks->sortBy('scheduled_at') as $task)
                    <div class="relative pl-4">
                        <div class="timeline-dot"></div>
                        <div class="glass-card mb-4 hover:border-cyan-500/50 transition-all">
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-grow">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="text-[10px] text-cyan-400 font-bold uppercase tracking-widest">
                                            {{ $task->scheduled_at ? $task->scheduled_at->format('l, d M Y') : 'SIN FECHA' }}
                                        </div>
                                        @if($task->is_completed)
                                            <span class="px-2 py-0.5 rounded-full bg-green-500/20 text-green-400 text-[8px] font-bold uppercase">Completada</span>
                                        @endif
                                    </div>
                                    <h4 class="text-xl font-bold text-white group-hover:text-cyan-400 transition-colors">{{ $task->title }}</h4>
                                    @if($task->description)
                                        <p class="text-sm text-white/50 mt-2 leading-relaxed">{{ $task->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-6 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-white/5 pt-4 md:pt-0">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-white tracking-widest">{{ $task->scheduled_at ? $task->scheduled_at->format('H:i') : '--:--' }}</div>
                                        @if($task->scheduled_at)
                                            <div class="text-[10px] text-white/20 uppercase tracking-tighter">{{ $task->scheduled_at->diffForHumans() }}</div>
                                        @endif
                                    </div>
                                    @if($forecast = $task->getForecast())
                                        <div class="flex items-center gap-2 bg-white/5 px-3 py-2 rounded-xl border border-white/10">
                                            <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png" class="w-8 h-8">
                                            <div class="text-right">
                                                <div class="text-xs font-bold text-cyan-400">{{ round($forecast['main']['temp']) }}°C</div>
                                                <div class="text-[8px] text-white/40 uppercase">{{ $forecast['weather'][0]['description'] }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('tasks.edit', $task) }}" class="p-2 bg-white/5 hover:bg-cyan-500/20 rounded-lg text-white/40 hover:text-cyan-400 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="glass-card text-center py-12 text-white/20 uppercase italic tracking-[0.3em]">
                        Timeline Vacío
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
