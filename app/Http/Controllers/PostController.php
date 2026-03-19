<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::orderBy('created_at', 'desc')->get();

        foreach ($posts as $post) {
            $post->image_url = $post->image
                ? asset('storage/' . $post->image)
                : null;
        }

        return response()->json([
            'status' => true,
            'data' => $posts,
            'message' => 'Lấy danh sách bài viết thành công',
        ]);
    }

    public function store(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                // 1. Lấy toàn bộ dữ liệu
                $data = $request->all();

                // 2. Xử lý SLUG (Bắt buộc phải có để tránh lỗi SQL Field 'slug' doesn't have a default value)
                if ($request->filled('title')) {
                    $baseSlug = Str::slug($request->title);
                    $slug = $baseSlug;
                    $count = 1;
                    // Kiểm tra trùng lặp slug trong DB
                    while (Post::where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $count;
                        $count++;
                    }
                    $data['slug'] = $slug;
                }

                // 3. Xử lý upload ảnh
                if ($request->hasFile('image')) {
                    $path = $request->file('image')->store('posts', 'public');
                    $data['image'] = $path;
                }

                // 4. BỔ SUNG CÁC TRƯỜNG MẶC ĐỊNH (Tránh lỗi 500 do Database yêu cầu NOT NULL)
                $data['topic_id'] = $request->input('topic_id', 1); // Mặc định topic số 1
                $data['description'] = $request->input('description', $request->input('title')); // Dùng title nếu trống
                $data['post_type'] = $request->input('post_type', 'post');
                $data['status'] = $request->input('status', 1);
                $data['created_at'] = now();
                $data['updated_at'] = now();
                $data['created_by'] = $request->input('created_by', 1); // Giả sử admin ID là 1

                // 5. Lưu vào database
                $post = Post::create($data);

                return response()->json([
                    'status' => true,
                    'data' => $post,
                    'message' => 'Tạo bài viết thành công',
                ], 201);
            });

        } catch (\Exception $e) {
            // Trả về lỗi chi tiết để debug trên Console/Network tab
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bài viết',
            ], 404);
        }

        $post->image_url = $post->image
            ? asset('storage/' . $post->image)
            : null;

        return response()->json([
            'status' => true,
            'data' => $post,
        ]);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        try {
            $data = $request->except(['image', '_method']);

            // Cập nhật slug nếu title thay đổi
            if ($request->filled('title') && $request->title !== $post->title) {
                $data['slug'] = Str::slug($request->title) . '-' . time();
            }

            if ($request->hasFile('image')) {
                // Xóa ảnh cũ
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }

                // Lưu ảnh mới
                $path = $request->file('image')->store('posts', 'public');
                $data['image'] = $path;
            }

            $data['updated_at'] = now();
            $post->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật thành công',
                'data' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['status' => true, 'message' => 'Xóa thành công']);
    }
}