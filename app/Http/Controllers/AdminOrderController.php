<?php

namespace App\Http\Controllers; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductStore;

class AdminOrderController extends Controller
{
    // 1. Lấy danh sách đơn hàng cho Admin
    public function index(Request $request)
    {
        // Sử dụng eager loading để tránh lỗi N+1 và fix lỗi relationship user
        $query = Order::with(['user', 'orderDetails']);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('id', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Lọc theo trạng thái (Xử lý trường hợp chuỗi "all")
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Sắp xếp đơn hàng mới nhất lên đầu
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    // 2. Cập nhật trạng thái (Duyệt đơn, Giao hàng...)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Đơn hàng không tồn tại'], 404);
        }

        $order->status = $request->input('status');
        $order->save();

        return response()->json([
            'status' => true, 
            'message' => 'Cập nhật trạng thái đơn hàng #' . $id . ' thành công'
        ]);
    }

    // 3. Hủy đơn và hoàn lại kho hàng
    public function cancel(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Đơn hàng không tồn tại'], 404);
        }

        // Không cho phép hủy đơn đã hoàn thành (status = 3) hoặc đã hủy (status = 4)
        if ($order->status >= 3) {
            return response()->json(['status' => false, 'message' => 'Không thể hủy đơn hàng này'], 400);
        }

        DB::beginTransaction();
        try {
            $order->status = 4; // Trạng thái hủy
            $order->note = $request->input('reason', 'Admin hủy đơn');
            $order->save();

            // Hoàn kho: Lấy chi tiết đơn hàng để cộng lại số lượng vào kho
            $details = OrderDetail::where('order_id', $id)->get();
            foreach ($details as $item) {
                // Tìm kho hàng tương ứng của sản phẩm
                $store = ProductStore::where('product_id', $item->product_id)->first();
                if ($store) {
                    $store->increment('qty', $item->qty);
                }
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Đã hủy đơn và hoàn kho thành công.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
}