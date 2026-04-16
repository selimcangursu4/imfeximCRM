<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // 1. Genel Bakış (Overview)
        $totalCustomers = Customer::count();
        $activeDealsCount = Customer::whereNotIn('status', [Customer::STATUS_WON, Customer::STATUS_LOST])->count();
        
        $todaySales = Customer::where('status', Customer::STATUS_WON)
            ->whereDate('updated_at', $today)
            ->sum('deal_value');
            
        $monthlySales = Customer::where('status', Customer::STATUS_WON)
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('deal_value');

        $wonDeals = Customer::where('status', Customer::STATUS_WON)->count();
        $lostDeals = Customer::where('status', Customer::STATUS_LOST)->count();
        $totalFinished = $wonDeals + $lostDeals;
        $conversionRate = $totalFinished > 0 ? round(($wonDeals / $totalFinished) * 100, 1) : 0;

        // Funnel Stats
        $funnelData = Customer::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
        $funnelLabels = array_keys($funnelData);
        $funnelValues = array_values($funnelData);

        // 2. Mesajlar & İletişim (Omnichannel)
        $recentConversations = Conversation::with(['latestMessage', 'customer.companyRelation'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
            
        // 3. Görevler & Hatırlatmalar
        $todayTasks = Task::with('customer')
            ->whereDate('due_date', $today)
            ->where('status', '!=', 'completed')
            ->get();
            
        $overdueTasks = Task::with('customer')
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'completed')
            ->get();

        // 4. Müşteri (Lead) Akışı
        $recentLeads = Customer::with('companyRelation')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 5. Performans Analizi
        $topPerformers = DB::table('customers')
            ->join('users', 'customers.assigned_user_id', '=', 'users.id')
            ->select('users.name', DB::raw('SUM(customers.deal_value) as total_sales'), DB::raw('COUNT(customers.id) as deals_won'))
            ->where('customers.status', Customer::STATUS_WON)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->take(5)
            ->get();

        // Chart Data (Son 7 gün satış)
        $salesChartLabels = [];
        $salesChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sum = Customer::where('status', Customer::STATUS_WON)
                ->whereDate('updated_at', $date)
                ->sum('deal_value');
            $salesChartLabels[] = $date->format('d M');
            $salesChartData[] = $sum;
        }

        return view('dashboard', compact(
            'totalCustomers', 'activeDealsCount', 'todaySales', 'monthlySales',
            'wonDeals', 'lostDeals', 'conversionRate', 
            'funnelLabels', 'funnelValues',
            'recentConversations', 
            'todayTasks', 'overdueTasks',
            'recentLeads', 'topPerformers', 'salesChartLabels', 'salesChartData'
        ));
    }
}
