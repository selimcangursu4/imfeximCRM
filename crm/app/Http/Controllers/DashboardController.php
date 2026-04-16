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
        $user = auth()->user();
        $isAdmin = in_array($user->role ?? 'satis_danismani', ['admin', 'yonetici']);

        // Müşteri query base'i
        $customerQuery = Customer::query();
        if (!$isAdmin) {
            // Sadece kendisine atananlar
            $customerQuery->where('assigned_user_id', $user->id);
        }

        // 1. Genel Bakış (Overview)
        $totalCustomers = (clone $customerQuery)->count();
        $activeDealsCount = (clone $customerQuery)->whereNotIn('status', [Customer::STATUS_WON, Customer::STATUS_LOST])->count();
        
        $todaySales = (clone $customerQuery)->where('status', Customer::STATUS_WON)
            ->whereDate('updated_at', $today)
            ->sum('deal_value');
            
        $monthlySales = (clone $customerQuery)->where('status', Customer::STATUS_WON)
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('deal_value');

        $wonDeals = (clone $customerQuery)->where('status', Customer::STATUS_WON)->count();
        $lostDeals = (clone $customerQuery)->where('status', Customer::STATUS_LOST)->count();
        $totalFinished = $wonDeals + $lostDeals;
        $conversionRate = $totalFinished > 0 ? round(($wonDeals / $totalFinished) * 100, 1) : 0;

        // Funnel Stats
        $funnelData = (clone $customerQuery)->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
        $funnelLabels = array_keys($funnelData);
        $funnelValues = array_values($funnelData);

        // 2. Mesajlar & İletişim (Omnichannel)
        $conversationQuery = Conversation::with(['latestMessage', 'customer.companyRelation']);
        if (!$isAdmin) {
            $conversationQuery->whereHas('customer', function($q) use ($user) {
                $q->where('assigned_user_id', $user->id);
            });
        }
        $recentConversations = $conversationQuery->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
            
        // 3. Görevler & Hatırlatmalar
        $taskQuery = Task::with('customer');
        if (!$isAdmin) {
            $taskQuery->where('assigned_to', $user->id);
        }
        $todayTasks = (clone $taskQuery)
            ->whereDate('due_date', $today)
            ->where('status', '!=', 'completed')
            ->get();
            
        $overdueTasks = (clone $taskQuery)
            ->where('due_date', '<', $today)
            ->where('status', '!=', 'completed')
            ->get();

        // 4. Müşteri (Lead) Akışı
        $recentLeads = (clone $customerQuery)->with('companyRelation')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 5. Performans Analizi (Sadece yoneticiler görecek, personeller boş veya sadece kendini görecek)
        $topPerformers = collect();
        if ($isAdmin) {
            $topPerformers = DB::table('customers')
                ->join('users', 'customers.assigned_user_id', '=', 'users.id')
                ->select('users.name', DB::raw('SUM(customers.deal_value) as total_sales'), DB::raw('COUNT(customers.id) as deals_won'))
                ->where('customers.status', Customer::STATUS_WON)
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total_sales')
                ->take(5)
                ->get();
        }

        // Chart Data (Son 7 gün satış)
        $salesChartLabels = [];
        $salesChartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $sum = (clone $customerQuery)->where('status', Customer::STATUS_WON)
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
