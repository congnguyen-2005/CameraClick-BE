<?php

namespace App\Http\Controllers;

use App\Models\ProductStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductStoreController extends Controller
{
    public function index() {
    return ProductStore::with('product')->whereNull('type')->get();
}
    // ================= NHẬP KHO (IMPORT) =================
    public function import(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'qty' => 'required|integer|min:1',
                'price_root' => 'required|numeric',
            ]);

            // 1. TÌM HOẶC KHỞI TẠO (Thay đổi quan trọng ở đây)
            // Tìm bản ghi kho gốc (type = null). Nếu không thấy -> Tự tạo object mới (chưa lưu DB)
            $store = ProductStore::firstOrNew(
                ['product_id' => $request->product_id, 'type' => null]
            );

            // Nếu là bản ghi mới tinh (vừa được new ra), set qty mặc định là 0
            if (!$store->exists) {
                $store->qty = 0;
            }

            // 2. CỘNG DỒN SỐ LƯỢNG & CẬP NHẬT GIÁ
            $store->qty += $request->qty;
            $store->price_root = $request->price_root; // Cập nhật giá vốn mới nhất
            $store->save(); // Lưu vào DB (Lúc này mới chính thức tạo hoặc update)

            // 3. GHI LOG LỊCH SỬ NHẬP
            ProductStore::create([
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'price_root' => $request->price_root,
                'type' => 'import',
                'created_by' => Auth::id() ?? 1 
            ]);

            return response()->json(['status' => true, 'message' => 'Nhập kho thành công (Đã cập nhật tồn kho)']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // ================= XUẤT KHO (EXPORT) =================
    public function export(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'qty' => 'required|integer|min:1',
            ]);

            $store = ProductStore::where('product_id', $request->product_id)
                                 ->whereNull('type')
                                 ->first();

            if (!$store || $store->qty < $request->qty) {
                return response()->json(['status' => false, 'message' => 'Không đủ tồn kho thực tế'], 400);
            }

            $store->decrement('qty', $request->qty);

            // Ghi lịch sử xuất
            ProductStore::create([
                'product_id' => $request->product_id,
                'qty' => $request->qty,
                'price_root' => $store->price_root ?? 0,
                'type' => 'export',
                'created_by' => auth()->id() ?? 1
            ]);

            return response()->json(['status' => true, 'message' => 'Xuất kho thành công']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ================= LỊCH SỬ KHO (HISTORY) =================
   public function history() 
{
    try {
        // Chỉ lấy các bản ghi là Lịch sử (type là import hoặc export)
        // Không lấy bản ghi số dư tổng (type = null)
        $data = ProductStore::with('product')
                            ->whereNotNull('type') // 
                            ->orderBy('created_at', 'desc')
                            ->get(); 

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}
}