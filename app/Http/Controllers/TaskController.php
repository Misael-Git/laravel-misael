<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->tasks();

        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $tasks = $query->orderBy('scheduled_at', 'asc')->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'auto_complete' => 'boolean',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'scheduled_at' => 'nullable|date',
        ]);

        // Default to user coordinates if not provided
        if (empty($validated['lat']) || empty($validated['lng'])) {
            $validated['lat'] = auth()->user()->lat;
            $validated['lng'] = auth()->user()->lng;
        }

        $request->user()->tasks()->create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada con éxito.');
    }

    public function show(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_completed' => 'boolean',
            'auto_complete' => 'boolean',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'scheduled_at' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }

    /**
     * Alternar el estado de completado de una tarea.
     */
    public function toggle(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return back()->with('success', 'Estado de la tarea actualizado.');
    }
}
