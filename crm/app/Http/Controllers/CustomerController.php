<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->paginate(15);

        return view('customers.index', compact('customers'));
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
