<?php

namespace App\Http\Controllers;

use App\Models\ProductSale;
use Illuminate\Http\Request;

class ProductSaleController extends Controller
{
   public function index(Request $request)
{
    // Sử dụng 'with' để lấy thông tin sản phẩm kèm theo (bao gồm name, thumbnail...)
    $query = ProductSale::with(['product' => function($q) {
        $q->select('id', 'name', 'thumbnail', 'price_buy');
    }]);

    if ($request->product_id) {
        $query->where('product_id', $request->product_id);
    }

    // Sắp xếp theo ID giảm dần để khuyến mãi mới nhất lên đầu
    $sales = $query->orderBy('id', 'desc')->get();

    return response()->json([
        'status' => true,
        'data' => $sales,
        'message' => 'Lấy danh sách khuyến mãi thành công'
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'name'       => 'required|string|max:255',
        'product_id' => 'required|integer|exists:products,id',
        'price_sale' => 'required|numeric|min:0',
        'date_begin' => 'required|date',
        'date_end'   => 'required|date|after:date_begin',
    ]);

    // LOGIC: Kiểm tra sản phẩm có đang bị trùng lịch sale không
    $exists = ProductSale::where('product_id', $request->product_id)
        ->where(function ($query) use ($request) {
            $query->whereBetween('date_begin', [$request->date_begin, $request->date_end])
                  ->orWhereBetween('date_end', [$request->date_begin, $request->date_end])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('date_begin', '<=', $request->date_begin)
                        ->where('date_end', '>=', $request->date_end);
                  });
        })->exists();

    if ($exists) {
        return response()->json([
            'status' => false,
            'message' => 'Sản phẩm này đã có lịch khuyến mãi trong khoảng thời gian đã chọn!'
        ], 422);
    }

    $sale = ProductSale::create($request->all());

    return response()->json([
        'status' => true,
        'data' => $sale,
        'message' => 'Tạo khuyến mãi thành công'
    ], 201);
}
    public function show($id)
    {
        $sale = ProductSale::find($id);

        if (!$sale) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khuyến mãi'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $sale
        ]);
    }

    public function update(Request $request, $id)
    {
        $sale = ProductSale::find($id);

        if (!$sale) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khuyến mãi'
            ], 404);
        }

        $sale->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $sale,
            'message' => 'Cập nhật khuyến mãi thành công'
        ]);
    }

    public function destroy($id)
    {
        $sale = ProductSale::find($id);

        if (!$sale) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy khuyến mãi'
            ], 404);
        }

        $sale->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa khuyến mãi thành công'
        ]);
    }
}
