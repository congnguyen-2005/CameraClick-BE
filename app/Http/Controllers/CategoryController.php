<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // ⚠️ Quan trọng: Phải import cái này để xóa ảnh

class CategoryController extends Controller
{
    // 🟢 CREATE: Thêm mới (Giữ nguyên logic của bạn)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:5000'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['parent_id']   = 0;
        $data['sort_order']  = 1;
        $data['description'] = $request->description ?? null;
        $data['status']      = 1;

        // Upload ảnh
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/category', 'public');
            $data['image'] = url('storage/' . $path); // Lưu đường dẫn đầy đủ
        }

        $category = Category::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Thêm thành công',
            'data' => $category
        ]);
    }

    // 🟡 UPDATE: Sửa (Thêm logic xóa ảnh cũ)
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:5000'
        ]);

        $data['slug'] = Str::slug($data['name']);

        // Nếu người dùng upload ảnh mới
        if ($request->hasFile('image')) {
            // 1. Xóa ảnh cũ nếu có
            if ($category->image) {
                // Chuyển URL đầy đủ thành đường dẫn tương đối để xóa
                // Ví dụ: http://localhost:8000/storage/uploads/xxx.jpg -> uploads/xxx.jpg
                $oldPath = str_replace(url('storage/'), '', $category->image);
                
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // 2. Upload ảnh mới
            $path = $request->file('image')->store('uploads/category', 'public');
            $data['image'] = url('storage/' . $path);
        }

        $category->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $category
        ]);
    }

    // 🔴 DELETE: Xóa (Thêm logic xóa file ảnh khỏi ổ cứng)
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }

        // Xóa ảnh trong thư mục
        if ($category->image) {
            $oldPath = str_replace(url('storage/'), '', $category->image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $category->delete();

        return response()->json(['status' => true, 'message' => 'Xóa thành công']);
    }

    // LIST & SHOW (Giữ nguyên)
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->get();
        return response()->json(['status' => true, 'data' => $categories]);
    }

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        return response()->json(['status' => true, 'data' => $category]);
    }
}