<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['project'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $projects = Project::orderBy('project_name')->get();
        
        // Calculate task statistics
        $taskStats = [
            'total' => Task::count(),
            'completed' => Task::where('status', 'Completed')->count(),
            'in_progress' => Task::where('status', 'In Progress')->count(),
            'overdue' => Task::where('end_date', '<', now())
                ->whereNotIn('status', ['Completed', 'Cancelled'])
                ->count(),
        ];
        
        return view('tasks.index', compact('tasks', 'projects', 'taskStats'));
    }

    public function create()
    {
        $projects = Project::orderBy('project_name')->get();
        return view('tasks.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,In Progress,On Hold,Completed,Cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
        ]);

        // Convert priority string to integer
        $priorityMap = ['High' => 1, 'Medium' => 2, 'Low' => 3];
        $validated['priority'] = $priorityMap[$validated['priority']];

        $task = Task::create($validated);

        if ($request->expectsJson()) {
            return response()->json($task->load('project'), 201);
        }

        return redirect()->route('projects.show', $validated['project_id'])
            ->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $task->load(['project', 'resources', 'documents']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $projects = Project::orderBy('project_name')->get();
        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'project_id' => 'required|exists:projects,id',
            'description' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status' => 'required|in:Not Started,In Progress,On Hold,Completed,Cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|integer|min:0',
            'progress_percent' => 'nullable|integer|min:0|max:100',
        ]);

        // Convert priority string to integer
        $priorityMap = ['High' => 1, 'Medium' => 2, 'Low' => 3];
        $validated['priority'] = $priorityMap[$validated['priority']];

        $task->update($validated);

        if ($request->expectsJson()) {
            return response()->json($task->load('project'));
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Task deleted successfully.']);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}

