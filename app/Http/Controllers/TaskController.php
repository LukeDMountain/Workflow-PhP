<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasksQuery = Task::query();
        $filter = $request->query('filter', 'all');

        if ($filter === 'today') {
            $tasksQuery->where('completed', false)
                ->where(function ($query): void {
                    $query->whereDate('due_date', now()->toDateString())->orWhereNull('due_date');
                });
        } elseif ($filter === 'upcoming') {
            $tasksQuery->where('completed', false)->whereDate('due_date', '>', now()->toDateString());
        } elseif ($filter === 'completed') {
            $tasksQuery->where('completed', true);
        }

        $allTasks = Task::query();

        return view('tasks.index', [
            'tasks' => $tasksQuery->orderByRaw('due_date IS NULL')->orderBy('due_date')->get(),
            'filter' => $filter,
            'counts' => [
                'all' => (clone $allTasks)->count(),
                'today' => (clone $allTasks)->where('completed', false)->where(function ($query): void {
                    $query->whereDate('due_date', now()->toDateString())->orWhereNull('due_date');
                })->count(),
                'upcoming' => (clone $allTasks)->where('completed', false)->whereDate('due_date', '>', now()->toDateString())->count(),
                'completed' => (clone $allTasks)->where('completed', true)->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        Task::create([...$validated, 'description' => $validated['description'] ?? '']);

        return redirect()->route('tasks.index')->with('success', 'Tarefa adicionada à sua lista.');
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        if ($request->boolean('toggle')) {
            $task->update(['completed' => ! $task->completed]);
        } else {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:120'],
                'description' => ['nullable', 'string', 'max:500'],
                'due_date' => ['nullable', 'date'],
                'priority' => ['required', 'in:low,medium,high'],
            ]);

            $task->update([...$validated, 'description' => $validated['description'] ?? '']);
        }

        return back()->with('success', 'Tarefa atualizada.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('success', 'Tarefa removida.');
    }
}
