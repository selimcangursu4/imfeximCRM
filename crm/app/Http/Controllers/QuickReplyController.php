<?php

namespace App\Http\Controllers;

use App\Models\QuickReply;
use App\Models\Company;
use Illuminate\Http\Request;

class QuickReplyController extends Controller
{
    public function index()
    {
        return view('settings.quick-replies.index');
    }

    public function getData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1; // Fallback for dev

        $query = QuickReply::where('company_id', $companyId);

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Total records
        $totalRecords = QuickReply::where('company_id', $companyId)->count();
        $filteredRecords = $query->count();

        // Order
        $columns = ['id', 'title', 'content', 'created_at'];
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

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        QuickReply::updateOrCreate(
            ['id' => $request->id],
            [
                'company_id' => $companyId,
                'title' => $request->title,
                'content' => $request->content,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Hazır mesaj başarıyla kaydedildi.']);
    }

    public function destroy(QuickReply $quickReply)
    {
        $quickReply->delete();
        return response()->json(['success' => true, 'message' => 'Hazır mesaj silindi.']);
    }
    
    public function show(QuickReply $quickReply)
    {
        return response()->json(['success' => true, 'data' => $quickReply]);
    }
}
