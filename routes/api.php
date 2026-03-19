<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminOrderController;

/* ===== IMPORT CONTROLLERS ===== */
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductSaleController;
use App\Http\Controllers\ProductStoreController;
use App\Http\Controllers\TopicController;

use App\Http\Controllers\UserController;

/* ===== API ROUTES ===== */

/* 1. PRODUCT */

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/product_new', [ProductController::class, 'product_new']);


/* 2. CATEGORY */
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);
Route::post('/category', [CategoryController::class, 'store']);
Route::put('/category/{id}', [CategoryController::class, 'update']);
Route::delete('/category/{id}', [CategoryController::class, 'destroy']);
Route::get('/category/{id}/products', [CategoryController::class, 'productsByCategory']);



/* 3. BANNER */
Route::get('/banner', [BannerController::class, 'index']);
Route::post('/banner', [BannerController::class, 'store']);
Route::get('/banner/{id}', [BannerController::class, 'show']);
Route::put('/banner/{id}', [BannerController::class, 'update']);
Route::delete('/banner/{id}', [BannerController::class, 'destroy']);

/* 4. ATTRIBUTE */
Route::get('/attribute', [AttributeController::class, 'index']);
Route::get('/attribute/{id}', [AttributeController::class, 'show']);
Route::post('/attribute', [AttributeController::class, 'store']);
Route::put('/attribute/{id}', [AttributeController::class, 'update']);
Route::delete('/attribute/{id}', [AttributeController::class, 'destroy']);


/* 5. PRODUCT ATTRIBUTE */
Route::get('/product-attribute', [ProductAttributeController::class, 'index']);
Route::post('/product-attribute', [ProductAttributeController::class, 'store']);
Route::get('/product-attribute/{id}', [ProductAttributeController::class, 'show']);
Route::put('/product-attribute/{id}', [ProductAttributeController::class, 'update']);
Route::delete('/product-attribute/{id}', [ProductAttributeController::class, 'destroy']);

/* 6. PRODUCT IMAGE */
Route::get('/product-image', [ProductImageController::class, 'index']);
Route::post('/product-image', [ProductImageController::class, 'store']);
Route::get('/product-image/{id}', [ProductImageController::class, 'show']);
Route::put('/product-image/{id}', [ProductImageController::class, 'update']);
Route::delete('/product-image/{id}', [ProductImageController::class, 'destroy']);

/* 7. PRODUCT SALE */
Route::get('/product-sale', [ProductSaleController::class, 'index']);
Route::post('/product-sale', [ProductSaleController::class, 'store']);
Route::get('/product-sale/{id}', [ProductSaleController::class, 'show']);
Route::put('/product-sale/{id}', [ProductSaleController::class, 'update']);
Route::delete('/product-sale/{id}', [ProductSaleController::class, 'destroy']);

/* 8. PRODUCT STORE */
Route::get('/product-store', [ProductStoreController::class, 'index']);
Route::post('/product-store', [ProductStoreController::class, 'store']);
Route::get('/product-store/{id}', [ProductStoreController::class, 'show']);
Route::put('/product-store/{id}', [ProductStoreController::class, 'update']);
Route::delete('/product-store/{id}', [ProductStoreController::class, 'destroy']);
// routes/api.php
Route::get('inventory/history', [ProductStoreController::class, 'history']);
/* 9. MENU */
Route::get('/menu', [MenuController::class, 'index']);


/* 10. TOPIC */
Route::get('/topic', [TopicController::class, 'index']);
Route::post('/topic', [TopicController::class, 'store']);
Route::get('/topic/{id}', [TopicController::class, 'show']);
Route::put('/topic/{id}', [TopicController::class, 'update']);
Route::delete('/topic/{id}', [TopicController::class, 'destroy']);

/* 11. POST */
Route::get('/post', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show']);
Route::delete('/post/{id}', [PostController::class, 'destroy']);
Route::post('/post', [PostController::class, 'store']);
Route::put('/post/{id}', [PostController::class, 'update']);





Route::prefix('contact')->group(function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::post('/', [ContactController::class, 'store']); 
    Route::get('/{id}', [ContactController::class, 'show']);
    Route::put('/{id}', [ContactController::class, 'update']);
    Route::delete('/{id}', [ContactController::class, 'destroy']);
});

/* 13. CONFIG */
Route::get('/config', [ConfigController::class, 'index']);




// Route cho Admin quản lý hoặc xem lịch sử
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::put('/orders/{id}', [OrderController::class, 'update']);
Route::get('/admin/orders', [OrderController::class, 'index']); 
    Route::put('/admin/orders/status/{id}', [OrderController::class, 'updateStatus']);

});



/* 15. ORDER DETAIL */
Route::get('/order-detail/{id}', [OrderDetailController::class, 'index']);
/* Route::post('/order-detail', [OrderDetailController::class, 'store']);
Route::put('/order-detail/{id}', [OrderDetailController::class, 'update']);
Route::delete('/order-detail/{id}', [OrderDetailController::class, 'destroy']); */



/* 16. USER CUSTOM */
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);


// routes/api.php
Route::prefix('inventory')->group(function () {
    Route::post('/import', [ProductStoreController::class, 'import']);
    Route::post('/export', [ProductStoreController::class, 'export']);
    Route::put('/stock/{id}', [ProductStoreController::class, 'update']);
    Route::delete('/stock/{id}', [ProductStoreController::class, 'destroy']);
    Route::get('/history', [ProductStoreController::class, 'history']);
    // ✅ SỬA Ở ĐÂY
    Route::get('/history-log', [InventoryController::class, 'historyLog']);

    Route::get('/export-excel', [InventoryController::class, 'exportExcel']);
});


// client 
Route::get('/products/category/{slug}', [ProductController::class, 'productListByCategory']);
Route::get('/products/category/{slug}', [ProductController::class, 'byCategory']);


// Trong routes/api.php
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('product-sales', ProductSaleController::class);
    Route::apiResource('product-images', ProductImageController::class);
});


// AUTHENTICATION ROUTES
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::post('/register', [AuthController::class, 'register']);


Route::get('/products-sale', [ProductController::class, 'getSaleProducts']);

Route::get('/products-home', [ProductController::class, 'getHomeData']);





// 2. PROTECTED ROUTES (Cần Token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Actions
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart Actions (Giỏ hàng lưu DB)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);             
        Route::post('/add', [CartController::class, 'store']);         
        Route::put('/update/{id}', [CartController::class, 'update']); 
        Route::delete('/remove/{id}', [CartController::class, 'destroy']); 
        Route::delete('/clear', [CartController::class, 'clear']);     
    });
});


Route::middleware(['auth:sanctum', 'checkadmin'])->group(function () {
    Route::apiResource('users', UserController::class);
});

Route::get('/vnpay-return', [OrderController::class, 'vnpayReturn']);


Route::middleware('auth:sanctum')->group(function () {
    // User Profile
    Route::get('/user', [AuthController::class, 'profile']);
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']); 
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);

    // Order History
    Route::get('/orders', [OrderController::class, 'myOrders']); 
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
});


Route::put('/order/cancel/{id}', [OrderController::class, 'cancelOrder']);


Route::middleware(['auth:sanctum', 'checkAdmin'])->prefix('admin')->group(function () {
    
    // Lấy danh sách đơn hàng (kèm lọc & tìm kiếm)
    Route::get('orders', [AdminOrderController::class, 'index']);

    // Xem chi tiết một đơn hàng
    Route::get('orders/{id}', [AdminOrderController::class, 'show']);

    // Cập nhật trạng thái (Duyệt, Giao hàng)
    Route::put('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    // Hủy đơn hàng (Kèm lý do & Hoàn kho)
    Route::put('orders/{id}/cancel', [AdminOrderController::class, 'cancel']);

});
Route::get('/users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum', 'checkAdmin'])->group(function () {
    // Route hủy đơn hàng dành cho Admin
    Route::put('/admin/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
});