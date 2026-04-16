<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->ajax()) {
            $tasks = Task::with('customer')
                ->where('assigned_to', $userId)
                ->orderBy('due_date', 'asc')
                ->get();

            return response()->json(['data' => $tasks]);
        }

        $customers = Customer::select('id', 'name')->get();
        return view('tasks.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'type' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        Task::create([
            'assigned_to' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'type' => $request->type,
            'customer_id' => $request->customer_id,
            'status' => 'pending',
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->assigned_to !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'status' => $request->status,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        if ($task->assigned_to !== Auth::id()) {
            abort(403);
        }

        $task->delete();
        return response()->json(['success' => true]);
    }
}
