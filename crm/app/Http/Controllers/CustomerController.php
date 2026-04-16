<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? 'satis_danismani', ['admin', 'yonetici']);

        $query = Customer::orderBy('created_at', 'desc');

        if (!$isAdmin) {
            $filter = $request->get('filter', 'my'); // my, pool
            if ($filter == 'my') {
                $query->where('assigned_user_id', $user->id);
            } else if ($filter == 'pool') {
                $query->whereNull('assigned_user_id');
            } else {
                $query->where('assigned_user_id', $user->id); // default fallback
            }
        }

        $customers = $query->paginate(15);
        // Filtreyi view'e gönder
        $currentFilter = $request->get('filter', 'my');

        return view('customers.index', compact('customers', 'isAdmin', 'currentFilter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        // Manuel oluşturulan müşteri o personelin üzerine atanır
        $data['assigned_user_id'] = auth()->id();
        $data['company_id'] = auth()->user()->company_id ?? 1;

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Müşteri başarıyla eklendi.');
    }

    public function assignToMe(Customer $customer)
    {
        if ($customer->assigned_user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bu müşteri zaten bir personele atanmış.'
            ], 400);
        }

        $customer->assigned_user_id = auth()->id();
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Müşteri başarıyla üzerinize alındı.'
        ]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Müşteri başarıyla güncellendi.');
    }

    public function updateAjax(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Müşteri bilgileri güncellendi.',
            'customer' => $customer
        ]);
    }

    public function startChat(Customer $customer)
    {
        $conversation = $customer->conversations()->latest()->first();

        if ($conversation) {
            return redirect()->route('omnichannel.index', ['conversation' => $conversation->id]);
        }

        return redirect()->route('omnichannel.index')->with('warning', 'Bu müşteri ile henüz bir konuşma başlatılmamış.');
    }
}
