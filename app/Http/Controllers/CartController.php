<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\ProductStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class CartController extends Controller
{
    // LẤY GIỎ HÀNG (Đã fix logic giá Sale)
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $now = Carbon::now('Asia/Ho_Chi_Minh');

            $cartItems = Cart::where('user_id', $userId)
                ->with(['product' => function ($query) {
                    $query->select('id', 'name', 'thumbnail', 'slug', 'price_buy');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            $cartItems->transform(function ($item) use ($now) {
                if ($item->product) {
                    $sale = DB::table('product_sales')
                        ->where('product_id', $item->product_id)
                        ->where('status', 1)
                        ->where('date_begin', '<=', $now)
                        ->where('date_end', '>=', $now)
                        ->first();
                    $item->product->price_sale = $sale ? $sale->price_sale : 0;
                }
                return $item;
            });

            return response()->json([
                'status' => true,
                'data' => $cartItems,
                'message' => 'Lấy giỏ hàng thành công'
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // THÊM VÀO GIỎ HÀNG (Sửa lỗi 500 và 422 ntc_products)
    public function store(Request $request)
    {
        // FIX: Đổi 'exists:products,id' thành 'exists:ntc_products,id'
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:ntc_products,id',
            'qty' => 'required|integer|min:1',
            'options' => 'nullable' 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $userId = $request->user()->id;
            $options = $request->input('options'); // Đây là một mảng ['Màu' => 'Đen']

            // 1. Kiểm tra tồn kho
            $store = ProductStore::where('product_id', $request->product_id)->first();
            if (!$store || $store->qty < $request->qty) {
                return response()->json(['status' => false, 'message' => 'Sản phẩm đã hết hàng hoặc không đủ số lượng'], 400);
            }

            // 2. Tìm sản phẩm trong giỏ (Trùng ID và Trùng cả Options mới cộng dồn)
            // Lưu ý: options được lưu dạng JSON trong DB
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $request->product_id)
                ->where('options', json_encode($options, JSON_UNESCAPED_UNICODE))
                ->first();

            if ($cartItem) {
                $cartItem->qty += $request->qty;
                $cartItem->save();
            } else {
                Cart::create([
                    'user_id' => $userId,
                    'product_id' => $request->product_id,
                    'qty' => $request->qty,
                    'options' => $options // Laravel tự động encode nhờ $casts ở Model
                ]);
            }

            return response()->json(['status' => true, 'message' => 'Đã thêm vào giỏ hàng thành công']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => 'Lỗi SQL: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) {
        $item = Cart::where('user_id', $request->user()->id)->where('id', $id)->first();
        if ($item) { 
            $item->qty = $request->qty; 
            $item->save(); 
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false, 'message' => 'Không tìm thấy mục này'], 404);
    }

    public function destroy(Request $request, $id) {
        Cart::where('user_id', $request->user()->id)->where('id', $id)->delete();
        return response()->json(['status' => true]);
    }
}