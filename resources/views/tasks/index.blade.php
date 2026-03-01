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
                        <div class="bg-cyan-500/10 border-l-4 border-cyan-500 text-cyan-400 p-4 mb-6 rounded-r-xl"
                            role="alert">
                            <p class="font-bold uppercase tracking-widest text-[10px]">{{ session('success') }}</p>
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

                    {{-- Horizontal Calendar Scroller --}}
                    @php
                        $startDate = now()->subDays(5);
                        $endDate = now()->addDays(30);
                        $currentDate = $startDate->copy();
                        
                        // Get dates that have tasks
                        $taskDates = Auth::user()->tasks()
                            ->whereBetween('scheduled_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                            ->get()
                            ->groupBy(fn($t) => $t->scheduled_at->format('Y-m-d'))
                            ->keys()
                            ->toArray();
                    @endphp

                    <div class="mb-10">
                        <div class="flex items-center gap-2 mb-4 overflow-x-auto pb-4 scrollbar-hide select-none" id="calendar-scroller">
                            @while($currentDate <= $endDate)
                                @php
                                    $isToday = $currentDate->isToday();
                                    $dateStr = $currentDate->format('Y-m-d');
                                    $isSelected = request('date') == $dateStr;
                                    $hasTask = in_array($dateStr, $taskDates);
                                    $isFirstOfMonth = $currentDate->day === 1;
                                @endphp

                                <div class="flex flex-col items-center flex-shrink-0">
                                    @if($isFirstOfMonth || $currentDate->isSameDay($startDate))
                                        <span class="text-[8px] text-cyan-400 font-black uppercase tracking-widest mb-2 px-2 bg-cyan-500/5 rounded border border-cyan-500/10">
                                            {{ $currentDate->translatedFormat('F') }}
                                        </span>
                                    @else
                                        <div class="h-[14px]"></div>
                                    @endif
                                    
                                    <a href="{{ route('tasks.index', ['date' => $dateStr]) }}" class="flex flex-col items-center p-3 rounded-2xl border transition-all duration-300 {{ $isSelected ? 'bg-cyan-500 border-cyan-400 shadow-[0_0_15px_#00f2ff] scale-110' : ($isToday ? 'bg-white/10 border-cyan-500/30' : 'bg-white/5 border-white/10 hover:border-cyan-500/30') }} min-w-[50px]">
                                        <span class="text-[7px] font-black uppercase tracking-tighter {{ $isSelected ? 'text-white' : 'text-white/30' }}">
                                            {{ $currentDate->translatedFormat('D') }}
                                        </span>
                                        <span class="text-lg font-black leading-none mt-1 text-white">
                                            {{ $currentDate->day }}
                                        </span>
                                        
                                        @if($hasTask)
                                            <div class="w-1 h-1 rounded-full mt-2 {{ $isSelected ? 'bg-white' : 'bg-cyan-500 shadow-[0_0_5px_#00f2ff]' }}"></div>
                                        @else
                                            <div class="h-1 mt-2"></div>
                                        @endif
                                    </a>
                                </div>
                                @php $currentDate->addDay(); @endphp
                            @endwhile
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-6">
                            <div>
                                <h3 class="neon-text text-2xl uppercase tracking-[0.2em] flex items-center gap-3">
                                    <span class="w-1.5 h-6 bg-cyan-500 rounded-full shadow-[0_0_10px_#00f2ff]"></span>
                                    Tareas
                                </h3>
                                <p class="text-white/40 text-[10px] uppercase font-black tracking-widest mt-1">
                                    @if(request('date'))
                                        Mostrando eventos del {{ \Carbon\Carbon::parse(request('date'))->translatedFormat('d \d\e F') }}
                                    @elseif(request('filter') === 'past')
                                        Mostrando tareas pasadas
                                    @else
                                        Base de datos de eventos y clima
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                @if(request('date') || request('filter') === 'past')
                                    <a href="{{ route('tasks.index') }}" class="text-[8px] bg-white/5 border border-white/10 hover:border-cyan-500/50 hover:text-cyan-400 px-3 py-1.5 rounded-lg font-black uppercase tracking-widest transition-all whitespace-nowrap">
                                        Ver todas las tareas →
                                    </a>
                                @endif
                                
                                @if(request('filter') !== 'past')
                                    <a href="{{ route('tasks.index', ['filter' => 'past']) }}" class="text-[8px] bg-white/5 border border-white/10 hover:border-cyan-500/50 hover:text-cyan-400 px-3 py-1.5 rounded-lg font-black uppercase tracking-widest transition-all whitespace-nowrap">
                                        Ver tareas pasadas
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if($isAdverse)
                            <div
                                class="px-4 py-2 bg-red-500/10 border border-red-500/30 rounded-xl flex items-center gap-2">
                                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                <span class="text-[8px] text-red-500 font-black uppercase tracking-widest">Creación
                                    Bloqueada</span>
                            </div>
                        @else
                            <a href="{{ route('tasks.create') }}" class="neon-button text-xs py-2 px-6">
                                + Nueva Tarea
                            </a>
                        @endif
                    </div>

                    @if($isAdverse)
                        <div
                            class="mb-8 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-[10px] text-red-400 font-bold uppercase tracking-widest leading-relaxed">
                            ⚠️ Los sensores detectan condiciones críticas
                            ({{ $globalWeather['weather'][0]['description'] }}).
                            El protocolo de inserción ha sido desactivado temporalmente.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse ($tasks as $task)
                            @php $forecast = $task->getForecast(); @endphp
                            <div class="glass-card group hover:border-cyan-500/40 transition-all {{ $task->is_completed ? 'opacity-50' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-grow pr-4">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-[8px] text-cyan-400 font-black uppercase tracking-[0.2em]">
                                                {{ $task->scheduled_at ? $task->scheduled_at->format('d M / H:i') : 'PND' }}
                                            </span>
                                            
                                            @if($forecast)
                                                <div title="Pronóstico para el {{ \Carbon\Carbon::createFromTimestamp($forecast['dt'])->translatedFormat('d M, H:i') }}" class="flex items-center gap-1.5 px-2 py-0.5 bg-white/5 hover:bg-white/10 transition-colors rounded-md border border-white/5 cursor-help">
                                                    <img src="https://openweathermap.org/img/wn/{{ $forecast['weather'][0]['icon'] }}.png" class="w-4 h-4" alt="weather">
                                                    <span class="text-[8px] text-white/60 font-bold">{{ round($forecast['main']['temp']) }}°C</span>
                                                    @if($task->isWeatherAdverse())
                                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse ml-1" title="Adverso: {{ $forecast['weather'][0]['description'] }}"></span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <h4
                                            class="text-sm font-bold text-white uppercase tracking-tight {{ $task->is_completed ? 'line-through' : '' }}">
                                            {{ $task->title }}
                                        </h4>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('tasks.toggle', $task) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="p-2 bg-white/5 hover:bg-green-500/20 rounded-lg transition-all {{ $task->is_completed ? 'text-green-500' : 'text-white/20' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('tasks.edit', $task) }}"
                                            class="p-2 bg-white/5 hover:bg-cyan-500/20 rounded-lg text-white/20 hover:text-cyan-400 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Static Notes --}}
                                @if($task->description)
                                    <div class="mt-4 pt-4 border-t border-white/5">
                                        <label
                                            class="block text-[7px] text-white/20 uppercase font-black mb-2 tracking-[0.2em]">Registro
                                            de Notas</label>
                                        <div
                                            class="text-[10px] text-white/60 leading-relaxed bg-white/5 p-3 rounded-lg border border-white/5 italic">
                                            {{ $task->description }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-2 border border-dashed border-white/10 rounded-2xl py-20 text-center">
                                <span class="text-[10px] text-white/10 font-black uppercase tracking-[0.5em]">No se han
                                    hallado registros</span>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scroller = document.getElementById('calendar-scroller');
            if (scroller) {
                const todayBtn = scroller.querySelector('.bg-cyan-500')?.parentElement;
                if (todayBtn) {
                    const scrollOffset = todayBtn.offsetLeft - (scroller.offsetWidth / 2) + (todayBtn.offsetWidth / 2);
                    scroller.scrollTo({
                        left: scrollOffset,
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
</x-app-layout>