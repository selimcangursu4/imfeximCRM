<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class SalesFunnelController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? 'satis_danismani', ['admin', 'yonetici']);

        $statuses = Customer::getStatuses();
        $funnelData = [];

        foreach ($statuses as $status) {
            $query = Customer::where('status', $status);
            if (!$isAdmin) {
                // Sadece yetkili olduğu müşteriler + Havuzdakiler de eklenebilir ama huni genellikle atananlar içindir
                $query->where('assigned_user_id', $user->id);
            }
            $funnelData[$status] = $query->get();
        }

        return view('sales-funnel.index', compact('funnelData', 'statuses'));
    }
}
