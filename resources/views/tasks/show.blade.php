<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Tarea') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Título</h3>
                        <p class="text-gray-600 border p-2 rounded bg-gray-50">{{ $task->title }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Descripción</h3>
                        <p class="text-gray-600 border p-2 rounded bg-gray-50 whitespace-pre-wrap">{{ $task->description ?: 'Sin descripción' }}</p>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Estado</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->is_completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $task->is_completed ? 'Completada' : 'Pendiente' }}
                        </span>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Fecha de Creación</h3>
                        <p class="text-gray-600">{{ $task->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="flex items-center mt-4">
                        <a href="{{ route('tasks.index') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Volver') }}
                        </a>
                        <a href="{{ route('tasks.edit', $task) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Editar') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
