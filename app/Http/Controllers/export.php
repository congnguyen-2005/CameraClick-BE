<?php
use Illuminate\Support\Facades\Gate;
use App\Models\ProductStore;
Class ExportController
{   public function export(Request $request)
{
    Gate::authorize('inventory-manage');

    $request->validate([
        'product_id' => 'required|integer',
        'qty' => 'required|integer|min:1',
    ]);

    $store = ProductStore::where('product_id', $request->product_id)->first();

    if (!$store || $store->qty < $request->qty) {
        return response()->json([
            'message' => 'Tồn kho không đủ',
            'current_qty' => $store?->qty ?? 0
        ], 422);
    }

    // trừ kho
}}

