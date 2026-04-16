<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Message;
use App\Models\Task;
use App\Models\MessageChannel;
use App\Services\AiReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 8 farklı raporlama modulunu sunan ana ekran
        return view('reports.index');
    }

    public function sales()
    {
        $wonCount = Customer::where('status', 'Kazanıldı')->count();
        $lostCount = Customer::where('status', 'Kaybedildi')->count();
        $totalFinished = $wonCount + $lostCount;
        $winRate = $totalFinished > 0 ? round(($wonCount / $totalFinished) * 100, 2) : 0;

        $avgDealSize = Customer::where('status', 'Kazanıldı')->avg('deal_value') ?? 0;
        $totalSalesAmount = Customer::where('status', 'Kazanıldı')->sum('deal_value') ?? 0;

        return view('reports.sales', compact('wonCount', 'lostCount', 'winRate', 'avgDealSize', 'totalSalesAmount'));
    }

    public function funnel()
    {
        $funnelStats = Customer::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();

        return view('reports.funnel', compact('funnelStats'));
    }

    public function leads()
    {
        $todayLeads = Customer::whereDate('created_at', Carbon::today())->count();
        $weekLeads = Customer::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $monthLeads = Customer::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        // Lead Sources
        $sourceStats = Customer::select('source', DB::raw('count(*) as count'))
            ->groupBy('source')
            ->pluck('count', 'source')->toArray();

        // Lead -> Müşteri / Kazanıldı Oranı (Genel)
        $totalLeads = Customer::count();
        $wonDeals = Customer::where('status', 'Kazanıldı')->count();
        $conversionRate = $totalLeads > 0 ? round(($wonDeals / $totalLeads) * 100, 2) : 0;

        return view('reports.leads', compact('todayLeads', 'weekLeads', 'monthLeads', 'sourceStats', 'conversionRate'));
    }

    public function activities()
    {
        // Tasks completed vs pending
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();
        
        // Agent performance
        $repStats = DB::table('users')
            ->leftJoin('tasks', 'users.id', '=', 'tasks.assigned_to')
            ->select('users.name', 
                DB::raw("SUM(CASE WHEN tasks.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks"),
                DB::raw("SUM(CASE WHEN tasks.status != 'completed' THEN 1 ELSE 0 END) as pending_tasks")
            )
            ->groupBy('users.id', 'users.name')
            ->get();

        return view('reports.activities', compact('completedTasks', 'pendingTasks', 'repStats'));
    }

    public function time()
    {
        // Pipeline'da ortalama bekleme süresi -> Şimdilik Kazanılanların Kapanış - Lead Oluşma (Gün bazında)
        $wonCustomers = Customer::where('status', 'Kazanıldı')->get();
        $totalDays = 0;
        foreach($wonCustomers as $c) {
            $totalDays += $c->updated_at->diffInDays($c->created_at);
        }
        $avgClosingTime = $wonCustomers->count() > 0 ? round($totalDays / $wonCustomers->count(), 1) : 0;

        return view('reports.time', compact('avgClosingTime', 'wonCustomers'));
    }

    public function revenue()
    {
        // Gerçekleşen Gelir
        $actualRevenue = Customer::where('status', 'Kazanıldı')->sum('deal_value');
        // Beklenen Gelir (Pipeline) -> Kazanıldı veya Kaybedildi dışındaki aktif fırsat değerleri
        $expectedRevenue = Customer::whereNotIn('status', ['Kazanıldı', 'Kaybedildi'])->sum('deal_value');

        return view('reports.revenue', compact('actualRevenue', 'expectedRevenue'));
    }

    public function marketing()
    {
        // Hangi kanal (Omnichannel) üzerinden gelmişse channel count
        $channelStats = DB::table('conversations')
            ->join('message_channels', 'conversations.message_channel_id', '=', 'message_channels.id')
            ->select('message_channels.name', DB::raw('count(conversations.id) as count'))
            ->groupBy('message_channels.name')
            ->pluck('count', 'name')->toArray();

        return view('reports.marketing', compact('channelStats'));
    }

    public function aiInsights(AiReportService $aiService)
    {
        // Tüm rapor verilerini derle JSON'la
        $data = [
            'total_customers' => Customer::count(),
            'revenue' => Customer::where('status', 'Kazanıldı')->sum('deal_value'),
            'funnel' => Customer::select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status'),
            'lead_sources' => Customer::select('source', DB::raw('count(*) as count'))->groupBy('source')->pluck('count', 'source'),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'open_tasks' => Task::where('status', '!=', 'completed')->count()
        ];
        
        // Bu işlem zaman alabilir diye bir cache konulabilir ama şimdilik canlı üretsin
        $insightText = clone $aiService; // Just resolving via container
        $insightText = $aiService->generateInsights($data);

        return view('reports.ai-insights', compact('insightText'));
    }
}
