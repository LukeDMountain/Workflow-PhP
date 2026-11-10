<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $tasks = collect($request->session()->get('tasks', []))->values();
        $filter = $request->query('filter', 'all');

        if (in_array($filter, ['today', 'upcoming', 'completed'], true)) {
            $tasks = $tasks->filter(function (array $task) use ($filter): bool {
                return match ($filter) {
                    'today' => ! $task['completed'] && ($task['due_date'] === now()->toDateString() || $task['due_date'] === null),
                    'upcoming' => ! $task['completed'] && $task['due_date'] !== null && $task['due_date'] > now()->toDateString(),
                    'completed' => $task['completed'],
                };
            });
        }

        return view('tasks.index', [
            'tasks' => $tasks->sortBy(fn (array $task): string => $task['due_date'] ?? '9999-12-31')->values(),
            'filter' => $filter,
            'counts' => [
                'all' => collect($request->session()->get('tasks', []))->count(),
                'today' => collect($request->session()->get('tasks', []))->filter(fn (array $task): bool => ! $task['completed'] && ($task['due_date'] === now()->toDateString() || $task['due_date'] === null))->count(),
                'upcoming' => collect($request->session()->get('tasks', []))->filter(fn (array $task): bool => ! $task['completed'] && $task['due_date'] !== null && $task['due_date'] > now()->toDateString())->count(),
                'completed' => collect($request->session()->get('tasks', []))->where('completed', true)->count(),
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

        $tasks = $request->session()->get('tasks', []);
        $tasks[] = [
            'id' => (string) Str::uuid(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? '',
            'due_date' => $validated['due_date'] ?? null,
            'priority' => $validated['priority'],
            'completed' => false,
        ];
        $request->session()->put('tasks', $tasks);

        return redirect()->route('tasks.index')->with('success', 'Tarefa adicionada à sua lista.');
    }

    public function update(Request $request, string $task): RedirectResponse
    {
        $tasks = $request->session()->get('tasks', []);

        foreach ($tasks as &$item) {
            if ($item['id'] === $task) {
                if ($request->has('toggle')) {
                    $item['completed'] = ! $item['completed'];
                } else {
                    $validated = $request->validate([
                        'title' => ['required', 'string', 'max:120'],
                        'description' => ['nullable', 'string', 'max:500'],
                        'due_date' => ['nullable', 'date'],
                        'priority' => ['required', 'in:low,medium,high'],
                    ]);
                    $item = array_merge($item, $validated, ['description' => $validated['description'] ?? '']);
                }

                $request->session()->put('tasks', $tasks);

                return back()->with('success', 'Tarefa atualizada.');
            }
        }

        return back()->with('error', 'Não encontramos essa tarefa.');
    }

    public function destroy(Request $request, string $task): RedirectResponse
    {
        $tasks = collect($request->session()->get('tasks', []))->reject(fn (array $item): bool => $item['id'] === $task)->values()->all();
        $request->session()->put('tasks', $tasks);

        return back()->with('success', 'Tarefa removida.');
    }
}
