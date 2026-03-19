<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ProductStore;

class OrderDetailController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderDetail::query();

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $details = $query->get();

        return response()->json([
            'status' => true,
            'data' => $details,
            'message' => 'Lấy chi tiết đơn hàng thành công',
        ]);
    }

   public function store(Request $request)
{
    // 1. Validate dữ liệu đầu vào
    $request->validate([
        'order_id' => 'required|exists:orders,id',
        'product_id' => 'required|exists:products,id',
        'qty' => 'required|integer|min:1',
        'price' => 'required|numeric',
    ]);

    // Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu
    DB::beginTransaction();
    try {
        // 2. Kiểm tra tồn kho trước khi bán
        // Tìm bản ghi kho "Master" (type = null)
        $store = ProductStore::where('product_id', $request->product_id)
                             ->whereNull('type')
                             ->lockForUpdate() // Khóa dòng này lại để tránh xung đột khi nhiều người mua cùng lúc
                             ->first();

        // Nếu không có kho hoặc số lượng không đủ
        if (!$store || $store->qty < $request->qty) {
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm này hiện không đủ hàng trong kho.'
            ], 400);
        }

        // 3. Trừ kho
        $store->decrement('qty', $request->qty);

        // Ghi log xuất kho tự động (Optional - tuỳ logic bạn muốn lưu log bán hàng ở đâu)
        // ProductStore::create([
        //     'product_id' => $request->product_id,
        //     'qty' => $request->qty,
        //     'price_root' => $store->price_root,
        //     'type' => 'export', // Hoặc 'sale'
        //     'created_by' => auth()->id() ?? 1
        // ]);

        // 4. Tạo chi tiết đơn hàng
        $detail = OrderDetail::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'total_money' => $request->qty * $request->price // Nếu bảng có cột này
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'data' => $detail,
            'message' => 'Tạo chi tiết đơn hàng thành công',
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}

    public function show($id)
    {
        $detail = OrderDetail::find($id);

        if (!$detail) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chi tiết đơn hàng',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $detail,
        ]);
    }

    public function update(Request $request, $id)
    {
        $detail = OrderDetail::find($id);

        if (!$detail) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chi tiết đơn hàng',
            ], 404);
        }

        $detail->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $detail,
            'message' => 'Cập nhật chi tiết đơn hàng thành công',
        ]);
    }

    public function destroy($id)
    {
        $detail = OrderDetail::find($id);

        if (!$detail) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy chi tiết đơn hàng',
            ], 404);
        }

        $detail->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa chi tiết đơn hàng thành công',
        ]);
    }
}