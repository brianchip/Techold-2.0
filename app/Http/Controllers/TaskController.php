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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'assignee_name' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        // Generate task code
        $project = Project::find($validated['project_id']);
        $taskCount = Task::where('project_id', $validated['project_id'])->count() + 1;
        $validated['task_code'] = $project->project_code . '-T' . str_pad($taskCount, 3, '0', STR_PAD_LEFT);

        $task = Task::create($validated);

        if ($request->expectsJson()) {
            return response()->json($task->load('project'), 201);
        }

        return redirect()->route('tasks.index')
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'assignee_name' => 'nullable|string|max:255',
            'estimated_hours' => 'nullable|numeric|min:0',
            'progress_percent' => 'nullable|numeric|min:0|max:100',
        ]);

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

