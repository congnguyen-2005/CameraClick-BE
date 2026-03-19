<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function index(Request $request)
    {
        // Sử dụng 'with' để lấy luôn thông tin sản phẩm và tên thuộc tính gốc
        // Giả sử Model ProductAttribute của bạn có define relationship 'product' và 'attribute'
        $query = ProductAttribute::with(['product:id,name', 'attribute:id,name']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Sắp xếp theo ID mới nhất
        $items = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $items,
            'message' => 'Lấy danh sách thuộc tính sản phẩm thành công',
        ]);
    }

    public function store(Request $request)
    {
        $item = ProductAttribute::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $item,
            'message' => 'Thêm thuộc tính sản phẩm thành công',
        ], 201);
    }

    public function show($id)
    {
        $item = ProductAttribute::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản ghi',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $item,
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = ProductAttribute::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản ghi',
            ], 404);
        }

        $item->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $item,
            'message' => 'Cập nhật thuộc tính sản phẩm thành công',
        ]);
    }

    public function destroy($id)
    {
        $item = ProductAttribute::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản ghi',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thuộc tính sản phẩm thành công',
        ]);
    }
}