<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller {
 

public function store(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required',
        'content' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    }

    try {
        $contact = new Contact();
        $contact->name = $request->input('name');
        $contact->email = $request->input('email');
        $contact->phone = $request->input('phone');
        $contact->content = $request->input('content');
        
        // Gán giá trị mặc định cho các cột bắt buộc trong DB của bạn
        $contact->status = 1; 
        $contact->created_by = 1; 
        $contact->created_at = now(); 
        
        $contact->save();

        return response()->json([
            'status' => true,
            'message' => 'Gửi yêu cầu liên hệ thành công!'
        ], 201);
    } catch (\Exception $e) {
        // Trả về lỗi chi tiết để bạn xem trong tab Preview (F12)
        return response()->json([
            'status' => false, 
            'message' => 'Lỗi SQL: ' . $e->getMessage()
        ], 500);
    }
}
}