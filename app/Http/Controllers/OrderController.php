<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductStore;
use App\Models\Cart;
use App\Mail\OrderConfirmation;
use Carbon\Carbon;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * 1. XỬ LÝ ĐẶT HÀNG (CHECKOUT)
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer.name' => 'required|string|max:255',
            'customer.phone' => 'required|string|max:20',
            'customer.address' => 'required|string',
            'customer.email' => 'nullable|email',
            'order_details' => 'required|array|min:1',
            'order_details.*.product_id' => 'required|exists:products,id',
            'order_details.*.qty' => 'required|integer|min:1',
            // Không nên tin hoàn toàn vào total_money từ client gửi lên
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $customer = $request->input('customer');
        $details = $request->input('order_details');
        $payment_method = $request->input('payment_method', 'cod');
        $user = $request->user();
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        DB::beginTransaction();
        try {
            $calc_total_money = 0;
            $verified_details = [];

            // Bước 1: Kiểm tra tồn kho và lấy giá đúng nhất từ DB (bao gồm giá sale)
            foreach ($details as $item) {
                $product = Product::leftJoin('product_sales', function ($join) use ($now) {
                    $join->on('products.id', '=', 'product_sales.product_id')
                        ->where('product_sales.status', 1)
                        ->where('product_sales.date_begin', '<=', $now)
                        ->where('product_sales.date_end', '>=', $now);
                })
                    ->select('products.*', 'product_sales.price_sale')
                    ->find($item['product_id']);

                $store = ProductStore::where('product_id', $item['product_id'])->first();

                if (!$store || $store->qty < $item['qty']) {
                    return response()->json([
                        'status' => false,
                        'message' => "Sản phẩm {$product->name} hiện không đủ hàng."
                    ], 400);
                }

                // Lấy giá đúng: Ưu tiên giá sale, nếu không có lấy giá mua
                $current_price = ($product->price_sale > 0) ? $product->price_sale : $product->price_buy;
                $calc_total_money += ($current_price * $item['qty']);

                $verified_details[] = [
                    'product_id' => $product->id,
                    'price' => $current_price,
                    'qty' => $item['qty'],
                    'amount' => $current_price * $item['qty']
                ];
            }

            // Bước 2: Tạo đơn hàng với số tiền đã xác thực từ Server
            $order = Order::create([
                'user_id' => $user->id,
                'name' => $customer['name'],
                'email' => $customer['email'] ?? null, // Sẽ lưu null nếu trống (DB đã cho phép ở Bước 1)
                'phone' => $customer['phone'],
                'address' => $customer['address'],
                'note' => $customer['note'] ?? '',
                'total_money' => $calc_total_money ?? 0, // Đảm bảo luôn có giá trị số
                'payment_method' => $payment_method ?? 'cod',
                'status' => 0,
                'created_at' => now(),
            ]);

            // Bước 3: Tạo chi tiết đơn hàng
            foreach ($verified_details as $v_item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $v_item['product_id'],
                    'price' => $v_item['price'],
                    'qty' => $v_item['qty'],
                    'amount' => $v_item['amount'],
                    'discount' => 0
                ]);

                if ($payment_method === 'cod') {
                    $store = ProductStore::where('product_id', $v_item['product_id'])->first();
                    $store->qty -= $v_item['qty'];
                    $store->save();
                }
            }

            Cart::where('user_id', $user->id)->delete();
            DB::commit();

            // Bước 5: Xử lý VNPAY
            if ($payment_method === 'vnpay') {
                return response()->json([
                    'status' => true,
                    'payment_url' => $this->createVnpayUrl($order)
                ]);
            }

            $this->sendOrderMail($order);
            return response()->json(['status' => true, 'message' => 'Đặt hàng thành công!', 'order_id' => $order->id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout error: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Lỗi hệ thống.'], 500);
        }
    }

    /**
     * 2. TẠO URL VNPAY (Fix lỗi số tiền không hợp lệ)
     */
    protected function createVnpayUrl($order)
    {
        $vnp_TmnCode = "CGXZLS0Z";
        $vnp_HashSecret = "XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:3000/orders/vnpay-return";

        // ÉP KIỂU SỐ NGUYÊN TUYỆT ĐỐI - Quan trọng nhất
        $vnp_Amount = (int) round($order->total_money) * 100;

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => "Thanh toan don hang " . $order->id, // Bỏ dấu # để tránh lỗi encode
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $order->id . "_" . time(),
        );

        ksort($inputData);

        // Sử dụng cách nối chuỗi chuẩn của VNPAY
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * 3. XỬ LÝ KẾT QUẢ VNPAY TRẢ VỀ
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = "XNBCJFAKAZQSGTARRLGCHVZWCIOIGSHN";
        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            $order = Order::find($request->vnp_TxnRef);
            if ($order && $request->vnp_ResponseCode == '00') {
                DB::beginTransaction();
                try {
                    // Thanh toán thành công -> Cập nhật trạng thái & Trừ kho
                    $order->status = 1; // Chờ xác nhận
                    $order->save();

                    $details = OrderDetail::where('order_id', $order->id)->get();
                    foreach ($details as $item) {
                        $store = ProductStore::where('product_id', $item->product_id)->first();
                        if ($store) {
                            $store->qty -= $item->qty;
                            $store->save();
                        }
                    }

                    DB::commit();
                    $this->sendOrderMail($order);
                    return response()->json(['status' => true, 'message' => 'Thanh toán thành công']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['status' => false, 'message' => 'Lỗi trừ kho: ' . $e->getMessage()]);
                }
            }
            return response()->json(['status' => false, 'message' => 'Thanh toán không thành công']);
        }
        return response()->json(['status' => false, 'message' => 'Chữ ký không hợp lệ']);
    }

    /**
     * 4. HỦY ĐƠN HÀNG (Hoàn kho)
     */
    public function cancelOrder(Request $request, $id)
    {
        try {
            // 1. Kiểm tra sự tồn tại của đơn hàng
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Không tìm thấy đơn hàng mã #' . $id
                ], 404);
            }

            // 2. Logic kiểm tra trạng thái (Ví dụ: Đơn đã giao thành công thì không được hủy)
            if ($order->status == 2) { // 2 là Hoàn thành
                return response()->json([
                    'status' => false,
                    'message' => 'Đơn hàng đã hoàn thành, không thể thực hiện thao tác hủy!'
                ], 400);
            }

            // 3. Thực hiện hủy đơn hàng trong Transaction
            DB::beginTransaction();

            // Cập nhật trạng thái thành 4 (Hủy) và lưu lý do
            // Đảm bảo bảng orders đã có cột cancel_reason
            $order->status = 4;
            $order->cancel_reason = $request->input('reason', 'Admin chủ động hủy đơn');
            $order->updated_at = now();

            $order->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Hệ thống đã hủy đơn hàng #' . $id . ' thành công.',
                'data' => $order
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            // Trả về lỗi chi tiết để debug thay vì chỉ báo 500
            return response()->json([
                'status' => false,
                'message' => 'Lỗi SQL/Hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 5. LẤY LỊCH SỬ ĐƠN HÀNG
     */
    public function myOrders(Request $request)
    {
        $orders = Order::with('orderDetails.product')
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['status' => true, 'data' => $orders]);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['orderDetails.product'])
            ->where('user_id', $request->user()->id)
            ->find($id);

        if (!$order)
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['status' => true, 'data' => $order]);
    }

    protected function sendOrderMail($order)
    {
        if (!empty($order->email)) {
            try {
                $orderWithDetails = $order->load('orderDetails.product');
                Mail::to($order->email)->send(new OrderConfirmation($orderWithDetails));
            } catch (\Exception $e) {
                Log::error("Mail error: " . $e->getMessage());
            }
        }
    }
}