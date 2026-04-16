<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $docs = KnowledgeBase::orderBy('id', 'desc')->get();
            return response()->json(['data' => $docs]);
        }
        return view('knowledge-bases.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        KnowledgeBase::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return response()->json(['success' => true]);
    }

    public function show(KnowledgeBase $knowledgeBase)
    {
        return response()->json($knowledgeBase);
    }

    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $knowledgeBase->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(KnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->delete();
        return response()->json(['success' => true]);
    }
}
