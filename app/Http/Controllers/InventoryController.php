<?php

namespace App\Http\Controllers;

use App\Models\ProductStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InventoryHistory;

class InventoryController extends Controller
{
    // 📜 LỊCH SỬ KHO (Tổng quan tồn kho hiện tại)
    public function history()
    {
        $data = DB::table('product_stores')
            ->join('products', 'products.id', '=', 'product_stores.product_id')
            ->select(
                'product_stores.id',
                'product_stores.product_id',
                'products.name as product_name',
                'product_stores.qty',
                'product_stores.price_root',
                'product_stores.updated_at'
            )
            ->orderByDesc('product_stores.id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // 📥 NHẬP KHO
    public function import(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
            'price_root' => 'required|numeric|min:0',
        ]);

        // 1. Cập nhật hoặc tạo mới tồn kho
        $store = ProductStore::firstOrCreate(
            ['product_id' => $request->product_id],
            ['qty' => 0, 'price_root' => $request->price_root]
        );

        $store->qty += $request->qty;
        $store->price_root = $request->price_root;
        $store->save();

        // 2. Lưu lịch sử (SỬA: Đưa lên trước return)
        InventoryHistory::create([
            'product_id' => $request->product_id,
            'type' => 'import',
            'qty' => $request->qty,
            'price_root' => $request->price_root
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Nhập kho thành công',
            'data' => $store
        ]);
    }

    // 📤 XUẤT KHO
    public function export(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
        ]);

        $store = ProductStore::where('product_id', $request->product_id)->first();

        if (!$store || $store->qty < $request->qty) {
            return response()->json([
                'status' => false,
                'message' => 'Không đủ tồn kho'
            ], 400);
        }

        // 1. Trừ tồn kho
        $store->qty -= $request->qty;
        $store->save();

        // 2. Lưu lịch sử (SỬA: Đưa lên trước return)
        InventoryHistory::create([
            'product_id' => $request->product_id,
            'type' => 'export',
            'qty' => $request->qty,
            // Export thường không đổi giá vốn, hoặc lấy giá hiện tại nếu cần
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Xuất kho thành công',
            'data' => $store
        ]);
    }

    // ✏️ SỬA TỒN KHO
    public function update($id, Request $request)
    {
        $request->validate([
            'qty' => 'required|integer|min:0',
            'price_root' => 'required|numeric|min:0',
        ]);

        $store = ProductStore::findOrFail($id);

        $store->qty = $request->qty;
        $store->price_root = $request->price_root;
        $store->save();

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $store
        ]);
    }

    // 🗑 XÓA
    public function destroy($id)
    {
        ProductStore::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thành công'
        ]);
    }

    // 📜 LOG LỊCH SỬ (Fix lỗi 500 ở đây)
    public function historyLog(Request $request)
    {
        $query = DB::table('inventory_histories')
            ->leftJoin('products', 'products.id', '=', 'inventory_histories.product_id');

        // Nếu có product_id gửi lên thì lọc theo sản phẩm đó
        if ($request->has('product_id')) {
            $query->where('inventory_histories.product_id', $request->product_id);
        }

        $data = $query->select(
                'inventory_histories.id',
                'products.name as product_name',
                'inventory_histories.type',
                'inventory_histories.qty',
                'inventory_histories.price_root', // Thêm cột này vì DB có
                'inventory_histories.created_at'
                // ĐÃ XÓA: stock_before, stock_after vì DB không có
            )
            ->orderByDesc('inventory_histories.id')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}