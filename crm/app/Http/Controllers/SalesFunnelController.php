<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class SalesFunnelController extends Controller
{
    public function index()
    {
        $statuses = Customer::getStatuses();
        $funnelData = [];

        foreach ($statuses as $status) {
            $funnelData[$status] = Customer::where('status', $status)->get();
        }

        return view('sales-funnel.index', compact('funnelData', 'statuses'));
    }
}
