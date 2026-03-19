<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();

        return response()->json([
            'status' => true,
            'data' => $attributes,
            'message' => 'Lấy danh sách thuộc tính thành công',
        ]);
    }

    public function store(Request $request)
    {
        $attribute = Attribute::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $attribute,
            'message' => 'Tạo thuộc tính thành công',
        ], 201);
    }

    public function show($id)
    {
        $attribute = Attribute::find($id);

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thuộc tính',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $attribute,
        ]);
    }

    public function update(Request $request, $id)
    {
        $attribute = Attribute::find($id);

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thuộc tính',
            ], 404);
        }

        $attribute->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $attribute,
            'message' => 'Cập nhật thuộc tính thành công',
        ]);
    }

    public function destroy($id)
    {
        $attribute = Attribute::find($id);

        if (!$attribute) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy thuộc tính',
            ], 404);
        }

        $attribute->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa thuộc tính thành công',
        ]);
    }
}