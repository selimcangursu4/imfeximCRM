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
        $user = Auth::user();
        $isAdmin = in_array($user->role ?? 'satis_danismani', ['admin', 'yonetici']);

        if ($request->ajax()) {
            $query = Task::with('customer')->orderBy('due_date', 'asc');
            
            if (!$isAdmin) {
                // Sadece kendisine atananlar
                $query->where('assigned_to', $user->id);
            } else {
                // Admin filtre uyguladıysa
                if ($request->has('filter') && $request->filter == 'my') {
                    $query->where('assigned_to', $user->id);
                }
            }

            return response()->json(['data' => $query->get()]);
        }

        $customers = Customer::select('id', 'name')->get();
        // Return isAdmin to view
        return view('tasks.index', compact('customers', 'isAdmin'));
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
