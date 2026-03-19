<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $banners = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $banners,
            'message' => 'Lấy danh sách banner thành công',
        ]);
    }

public function store(Request $request)
{
    try {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:50048',
        ]);

        $data = $request->all();

        // 2. Xử lý lưu file
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Lưu vào storage/app/public/banners
            $path = $file->store('banners', 'public'); 
            $data['image'] = $path;
        }

        $banner = Banner::create($data);

        return response()->json([
            'status' => true,
            'data' => $banner
        ], 201);

    } catch (\Exception $e) {
        // Trả về lỗi chi tiết để Frontend hiển thị được
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    public function show($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $banner,
        ]);
    }

    public function update(Request $request, $id)
{
    $banner = Banner::find($id);
    if (!$banner) return response()->json(['status' => false], 404);

    $data = $request->all();

    if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu tồn tại
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        // Lưu ảnh mới
        $data['image'] = $request->file('image')->store('banners', 'public');
    }

    $banner->update($data);
    return response()->json(['status' => true, 'data' => $banner]);
}

    public function destroy($id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy banner',
            ], 404);
        }

        $banner->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa banner thành công',
        ]);
    }
}