<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function getData(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $query = Product::where('company_id', $companyId);

        // Search
        if ($search = $request->input('search.value')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalRecords = Product::where('company_id', $companyId)->count();
        $filteredRecords = $query->count();

        // Order
        $columns = ['id', 'name', 'price', 'is_active', 'created_at'];
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
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['company_id'] = auth()->user()->company_id ?? 1;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Product::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response()->json(['success' => true, 'message' => 'Ürün başarıyla kaydedildi.']);
    }

    public function show(Product $product)
    {
        if ($product->company_id !== (auth()->user()->company_id ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim.'], 403);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }
    public function edit(Product $product)
    {
        if ($product->company_id !== (auth()->user()->company_id ?? 1)) {
            abort(403, 'Yetkisiz erişim.');
        }
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->company_id !== (auth()->user()->company_id ?? 1)) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    public function destroy(Product $product)
    {
        if ($product->company_id !== (auth()->user()->company_id ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Yetkisiz erişim.'], 403);
        }

        $product->delete();
        return response()->json(['success' => true, 'message' => 'Ürün başarıyla silindi.']);
    }
}
