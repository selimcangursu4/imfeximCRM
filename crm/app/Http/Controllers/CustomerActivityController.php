<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerActivity;
use Illuminate\Http\Request;

class CustomerActivityController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'nullable|string|max:255',
        ]);

        if ($request->has('status') && $request->status) {
            $customer->update(['status' => $request->status]);
        }

        $activity = CustomerActivity::create([
            'customer_id' => $customer->id,
            'user_id' => auth()->id() ?? 1, // Fallback to 1 for testing if not auth
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aktivite başarıyla eklendi.',
            'activity' => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'user_name' => optional($activity->user)->name ?? 'Sistem',
                'created_at' => $activity->created_at->format('d.m.Y H:i'),
            ]
        ]);
    }

    public function destroy(CustomerActivity $activity)
    {
        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aktivite silindi.'
        ]);
    }
}
