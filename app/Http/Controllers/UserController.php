<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        if ($request->has('roles')) {
            $query->where('roles', $request->roles);
        }
        $users = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => true,
            'data' => $users,
            'message' => 'Lấy danh sách người dùng thành công'
        ]);
    }

   public function store(Request $request)
    {
        // 1. Cấu hình Validation chặt chẽ
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string',
            'username' => 'nullable|string|unique:users', // Cho phép null nhưng nếu có phải duy nhất
            'roles'    => 'nullable|string'
        ]);

        // 2. Trả về lỗi 422 kèm danh sách lỗi chi tiết
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors() // Frontend sẽ đọc cái này
            ], 422);
        }

        try {
            // 3. Xử lý logic tạo User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                // Tự động tạo username nếu frontend không gửi lên
                'username' => $request->username ?? explode('@', $request->email)[0] . rand(100, 999),
                'password' => Hash::make($request->password),
                'phone'    => $request->phone ?? '',
                'roles'    => $request->roles ?? 'customer',
                'status'   => 1,
            ]);

            return response()->json([
                'status' => true,
                'data' => $user,
                'message' => 'Tạo thành viên thành công'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }
        return response()->json(['status' => true, 'data' => $user]);
    }

    public function update(Request $request, $id) 
    {
        $user = User::findOrFail($id);
        $user->update([
            'name'   => $request->name ?? $user->name,
            'phone'  => $request->phone ?? $user->phone,
            'roles'  => $request->roles ?? $user->roles,
            'status' => $request->status ?? $user->status
        ]);
        return response()->json(['status' => true, 'message' => 'Cập nhật thành công']);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);
        }
        $user->delete();
        return response()->json(['status' => true, 'message' => 'Xóa người dùng thành công']);
    }
}