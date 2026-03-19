<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    // 1. LẤY DANH SÁCH ẢNH
    public function index(Request $request)
    {
        $query = ProductImage::query();

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $images = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $images,
            'message' => 'Lấy danh sách hình ảnh sản phẩm thành công',
        ]);
    }

    // 2. TẢI LÊN NHIỀU ẢNH (Fix lỗi 422)
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu gửi lên (Lưu ý: images là mảng)
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'images'     => 'required|array',
            'images.*'   => 'image|mimes:jpg,jpeg,png,webp|max:20480', // Hỗ trợ 20MB
            'alt'        => 'nullable|string|max:255',
            'title'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $uploadedImages = [];

        try {
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    // Lưu file vào storage/app/public/products
                    $path = $file->store('products', 'public');

                    // Tạo bản ghi trong DB
                    $image = ProductImage::create([
                        'product_id' => $request->product_id,
                        'image'      => 'storage/' . $path,
                        'alt'        => $request->alt,
                        'title'      => $request->title,
                    ]);

                    $uploadedImages[] = $image;
                }
            }

            return response()->json([
                'status' => true,
                'data'   => $uploadedImages,
                'message' => 'Đã tải lên ' . count($uploadedImages) . ' ảnh thành công',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }

    // 3. CHI TIẾT ẢNH
    public function show($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }

        return response()->json(['status' => true, 'data' => $image]);
    }

    // 4. CẬP NHẬT (Thường dùng cho sửa Alt/Title của 1 ảnh)
    public function update(Request $request, $id)
    {
        $productImage = ProductImage::find($id);
        if (!$productImage) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:ntc_products,id',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            'alt'        => 'nullable|string|max:255',
            'title'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $productImage->product_id = $request->product_id;
        $productImage->alt = $request->alt;
        $productImage->title = $request->title;

        if ($request->hasFile('image')) {
            // Xóa ảnh vật lý cũ
            $oldPath = str_replace('storage/', '', $productImage->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image')->store('products', 'public');
            $productImage->image = 'storage/' . $path;
        }

        $productImage->save();

        return response()->json([
            'status' => true,
            'data' => $productImage,
            'message' => 'Cập nhật thành công',
        ]);
    }

    // 5. XÓA ẢNH
    public function destroy($id)
    {
        $image = ProductImage::find($id);

        if (!$image) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }

        // Xóa file vật lý trước khi xóa record
        $filePath = str_replace('storage/', '', $image->image);
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa hình ảnh thành công',
        ]);
    }
}