<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lista de Tareas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
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
                        <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-bold animate-pulse">
                            ⚠️ ATENCIÓN: Debido a condiciones climáticas adversas ({{ $globalWeather['weather'][0]['description'] }}), la creación de nuevas tareas está temporalmente deshabilitada por seguridad.
                        </div>
                    @endif

                    <div class="mb-4">
                        @if($isAdverse)
                            <button disabled class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                                {{ __('Crear Nueva Tarea (Bloqueado por clima)') }}
                            </button>
                        @else
                            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Crear Nueva Tarea') }}
                            </a>
                        @endif
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Título</th>
                                    <th scope="col" class="px-6 py-3">Descripción</th>
                                    <th scope="col" class="px-6 py-3">Estado</th>
                                    <th scope="col" class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tasks as $task)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $task->title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ Str::limit($task->description, 50) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->is_completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
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
