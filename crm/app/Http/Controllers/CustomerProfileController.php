<?php

namespace App\Http\Controllers;

use App\Models\Customer;

class CustomerProfileController extends Controller
{
    public function show(Customer $customer)
    {
        $customer->load(['companyRelation', 'conversations.messages', 'attachments', 'activities.user']);

        return view('customers.show', compact('customer'));
    }
}
