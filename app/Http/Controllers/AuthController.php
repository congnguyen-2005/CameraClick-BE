<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * ĐĂNG KÝ (REGISTER)
     */
   public function register(Request $request)
    {
        try {
            // 1. Cấu hình Validate nghiêm ngặt
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:50',
                    'unique:users,username',
                    'regex:/^[a-zA-Z0-9]+$/' // Chỉ cho phép chữ và số
                ],
                'email' => 'required|email:rfc,dns|unique:users,email', // Kiểm tra mail thật qua DNS
                'phone' => [
                    'required',
                    'regex:/^0[0-9]{9}$/' // Phải bắt đầu bằng 0 và đủ 10 số
                ],
                'password' => [
                    'required',
                    'min:6',
                    'confirmed',
                    'regex:/^[a-zA-Z0-9]+$/' // Mật khẩu không có ký tự đặc biệt
                ],
            ], [
                // Custom thông báo lỗi tiếng Việt
                'username.regex' => 'Tên đăng nhập không được chứa ký tự đặc biệt.',
                'phone.regex' => 'Số điện thoại phải bắt đầu bằng số 0 và có đúng 10 chữ số.',
                'password.regex' => 'Mật khẩu chỉ được phép chứa chữ cái và chữ số.',
                'email.email' => 'Địa chỉ email không hợp lệ.',
                'email.unique' => 'Email này đã được đăng ký sử dụng.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 2. Tạo User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'roles' => 'customer',
                'status' => 1,
                'created_at' => now(),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Chào mừng thành viên Alpha mới!',
                'token' => $token,
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi máy chủ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ĐĂNG NHẬP (LOGIN)
     */
    public function login(Request $request)
    {
        // 1. Validate đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Kiểm tra tài khoản
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Email hoặc mật khẩu không chính xác'
            ], 401);
        }

        // 3. Lấy thông tin User và kiểm tra trạng thái
        $user = User::where('email', $request->email)->firstOrFail();

        if ($user->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Tài khoản của bạn đã bị khóa!'
            ], 403);
        }

        // 4. Tạo Token mới (Sanctum JWT)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles,
            ]
        ]);
    }

    /**
     * LẤY THÔNG TIN USER ĐANG ĐĂNG NHẬP (PROFILE)
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * ĐĂNG XUẤT (LOGOUT)
     */
   public function logout(Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['status' => true, 'message' => 'Logged out']);
}
/**
     * CẬP NHẬT HỒ SƠ (CÓ AVATAR)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user(); // Lấy user từ Token

        // 1. Validate
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // 2. Xử lý Avatar nếu có upload
            if ($request->hasFile('avatar')) {
                // Xóa ảnh cũ nếu có và không phải là ảnh mặc định (tuỳ logic)
                if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                    \Storage::disk('public')->delete($user->avatar);
                }
                
                // Lưu ảnh mới
                $path = $request->file('avatar')->store('uploads/avatars', 'public');
                $user->avatar = $path;
            }

            // 3. Cập nhật thông tin text
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->address = $request->address; // Đảm bảo DB có cột address
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật hồ sơ thành công',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ĐỔI MẬT KHẨU
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current' => 'required',
            'new' => 'required|min:6',
            'confirm' => 'required|same:new'
        ]);

        $user = $request->user();

        // 1. Kiểm tra mật khẩu cũ
        if (!Hash::check($request->current, $user->password)) {
            return response()->json([
                'status' => false, 
                'message' => 'Mật khẩu hiện tại không chính xác'
            ], 400);
        }

        // 2. Đổi mật khẩu
        $user->password = Hash::make($request->new);
        $user->save();

        return response()->json([
            'status' => true, 
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}