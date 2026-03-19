<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::all();

        return response()->json([
            'status' => true,
            'data' => $configs,
            'message' => 'Lấy cấu hình thành công',
        ]);
    }

    public function store(Request $request)
    {
        $config = Config::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $config,
            'message' => 'Tạo cấu hình thành công',
        ], 201);
    }

    public function show($id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy cấu hình',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $config,
        ]);
    }

    public function update(Request $request, $id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy cấu hình',
            ], 404);
        }

        $config->update($request->all());

        return response()->json([
            'status' => true,
            'data' => $config,
            'message' => 'Cập nhật cấu hình thành công',
        ]);
    }

    public function destroy($id)
    {
        $config = Config::find($id);

        if (!$config) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy cấu hình',
            ], 404);
        }

        $config->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa cấu hình thành công',
        ]);
    }
}