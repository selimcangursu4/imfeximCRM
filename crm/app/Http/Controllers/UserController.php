<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('settings.users.index');
    }

    public function getData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $query = User::where('company_id', $companyId);

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $totalRecords = User::where('company_id', $companyId)->count();
        $filteredRecords = $query->count();

        // Order
        $columns = ['id', 'name', 'email', 'role', 'department', 'created_at'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query->orderBy($orderColumn, $orderDir);

        // Paginate
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $data = $query->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->id),
            ],
            'role' => 'required|string|in:admin,yonetici,web_developer,satis_danismani',
            'department' => 'nullable|string|max:255',
        ];

        if (!$request->id) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $request->validate($rules);

        $data = [
            'company_id' => $companyId,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'department' => $request->department,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Kullanıcı başarıyla kaydedildi.']);
    }

    public function show(User $user)
    {
        // Simple security check
        if ($user->company_id !== (auth()->user()->company_id ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim.'], 403);
        }

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function destroy(User $user)
    {
        if ($user->company_id !== (auth()->user()->company_id ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim.'], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Kendinizi silemezsiniz.'], 422);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Kullanıcı silindi.']);
    }
}
