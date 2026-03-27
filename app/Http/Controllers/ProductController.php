<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStore;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
{
    // =================================================
    // 📌 DANH SÁCH SẢN PHẨM (CÓ QUÉT GIÁ SALE)
    // =================================================
    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $productTable = (new Product())->getTable(); 
        $saleTable = (new ProductSale())->getTable(); 

        $query = Product::query()
            ->with(['category', 'product_store']) 
            ->leftJoin($saleTable, function ($join) use ($now, $productTable, $saleTable) {
                $join->on("$productTable.id", '=', "$saleTable.product_id")
                    ->where("$saleTable.status", 1)
                    ->where("$saleTable.date_begin", '<=', $now)
                    ->where("$saleTable.date_end", '>=', $now);
            })
            ->select(
                "$productTable.*",
                "$saleTable.price_sale",
                "$saleTable.name as sale_name",
                "$saleTable.date_end as sale_end"
            );

        if ($request->filled('search')) {
            $query->where("$productTable.name", 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where("$productTable.category_id", $request->category_id);
        }

        $products = $query->orderBy("$productTable.id", 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách thành công',
            'data' => $products
        ]);
    }

    // =================================================
    // 📌 LẤY TẤT CẢ SẢN PHẨM ĐANG SALE (TRANG SALE)
    // =================================================
    public function getSaleProducts()
    {
        try {
            $now = Carbon::now('Asia/Ho_Chi_Minh');

            $products = DB::table('products')
                ->join('product_sales', 'products.id', '=', 'product_sales.product_id')
                ->join('categories', 'categories.id', '=', 'products.category_id') 
                ->where('products.status', 1)
                ->where('product_sales.status', 1)
                ->where('product_sales.date_begin', '<=', $now)
                ->where('product_sales.date_end', '>=', $now)
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.thumbnail',
                    'products.price_buy',
                    'categories.name as category_name', 
                    'product_sales.name as sale_name',
                    'product_sales.price_sale',
                    'product_sales.date_end'
                )
                ->orderBy('product_sales.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =================================================
    // 📌 THÊM SẢN PHẨM MỚI (LƯU LOCAL NHƯ BANNER)
    // =================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ntc_categories,id',
            'name' => 'required|string|max:255',
            'price_buy' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                $product = new Product();
                $product->name = $validated['name'];
                $product->slug = Str::slug($validated['name']) . '-' . time();
                $product->price_buy = $validated['price_buy'];
                $product->category_id = $validated['category_id'];
                $product->description = $request->description ?? '';
                $product->content = $request->content ?? '';
                $product->status = 1;
                
                // UPLOAD ẢNH LOCAL GIỐNG BANNER
                if ($request->hasFile('thumbnail')) {
                    $path = $request->file('thumbnail')->store('uploads/products', 'public');
                    $product->thumbnail = 'storage/' . $path;
                } else {
                    $product->thumbnail = ''; // Tránh lỗi 1364 nếu người dùng không up ảnh
                }

                $product->save();

                ProductStore::create([
                    'product_id' => $product->id,
                    'qty' => $validated['stock'] ?? 0,
                    'price_root' => $validated['price_buy'] ?? 0,
                ]);

                // XỬ LÝ THUỘC TÍNH 
                if ($request->has('attributes')) {
                    $syncData = [];
                    $attributes = $request->input('attributes', []);
                    if (is_string($attributes)) {
                        $attributes = json_decode($attributes, true);
                    }
                    if (is_array($attributes)) {
                        foreach ($attributes as $attr) {
                            if (is_array($attr) && !empty($attr['attribute_id']) && isset($attr['value'])) {
                                $syncData[$attr['attribute_id']] = ['value' => $attr['value']];
                            }
                        }
                    }
                    if (!empty($syncData)) {
                        $product->attributes()->sync($syncData);
                    }
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Thêm sản phẩm thành công',
                    'data' => $product
                ], 201);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false, 
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    // =================================================
    // 📌 CHI TIẾT SẢN PHẨM 
    // =================================================
    public function show($id)
    {
        try {
            $now = Carbon::now('Asia/Ho_Chi_Minh');

            $product = Product::where('id', $id)
                ->orWhere('slug', $id)
                ->first();

            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
            }

            try {
                $product->load(['category', 'product_store', 'attributes']); 
            } catch (\Throwable $e_relation) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Lỗi quan hệ DB: ' . $e_relation->getMessage()
                ], 500);
            }

            $sale = ProductSale::where('product_id', $product->id)
                ->where('status', 1)
                ->where('date_begin', '<=', $now)
                ->where('date_end', '>=', $now)
                ->first();

            $product->price_sale = $sale ? $sale->price_sale : null;
            $product->sale_name = $sale ? $sale->name : null;

            return response()->json([
                'status' => true,
                'data' => $product
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    // =================================================
    // 📌 CẬP NHẬT SẢN PHẨM (LOCAL UPLOAD)
    // =================================================
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
        }

        try {
            return DB::transaction(function () use ($request, $product) {
                
                // CẬP NHẬT ẢNH LOCAL
                if ($request->hasFile('thumbnail')) {
                    // Xóa ảnh cũ đi cho nhẹ server
                    if ($product->thumbnail) {
                        $oldPath = str_replace('storage/', '', $product->thumbnail);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    // Lưu ảnh mới
                    $path = $request->file('thumbnail')->store('uploads/products', 'public');
                    $product->thumbnail = 'storage/' . $path;
                }

                $product->fill($request->except(['thumbnail', 'attributes', 'stock', '_method']));

                if ($request->filled('name')) {
                    $product->slug = Str::slug($request->name) . '-' . $product->id;
                }

                $product->save();

                if ($request->has('stock')) {
                    ProductStore::updateOrCreate(
                        ['product_id' => $product->id],
                        [
                            'qty' => $request->stock ?? 0,
                            'price_root' => $request->price_buy ?? ($product->price_buy ?? 0)
                        ]
                    );
                }

                if ($request->has('attributes')) {
                    $syncData = [];
                    $attributes = $request->input('attributes', []);

                    if (is_string($attributes)) {
                        $attributes = json_decode($attributes, true);
                    }

                    if (is_array($attributes)) {
                        foreach ($attributes as $attr) {
                            if (is_array($attr) && !empty($attr['attribute_id']) && isset($attr['value'])) {
                                $syncData[$attr['attribute_id']] = ['value' => $attr['value']];
                            }
                        }
                    }
                    
                    $product->attributes()->sync($syncData);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Cập nhật sản phẩm thành công',
                    'data' => $product
                ]);
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi Server: ' . $e->getMessage()
            ], 500);
        }
    }

    // =================================================
    // 📌 XÓA SẢN PHẨM
    // =================================================
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['status' => false, 'message' => 'Không tìm thấy'], 404);

        // Xóa luôn file ảnh vật lý trên server khi xóa sản phẩm
        if ($product->thumbnail) {
            $oldPath = str_replace('storage/', '', $product->thumbnail);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        ProductStore::where('product_id', $id)->delete();
        ProductSale::where('product_id', $id)->delete();
        $product->attributes()->detach(); 
        $product->delete();

        return response()->json(['status' => true, 'message' => 'Xóa sản phẩm thành công']);
    }

    // =================================================
    // 📌 LẤY DATA TRANG HOME 
    // =================================================
    public function getHomeData()
    {
        $newProducts = $this->getProductByCriteria('latest', 8);
        $cameras = $this->getProductByCategorySlug('may-anh', 8);
        $lenses = $this->getProductByCategorySlug('ong-kinh', 8);

        return response()->json([
            'status' => true,
            'new_products' => $newProducts,
            'cameras' => $cameras,
            'lenses' => $lenses
        ]);
    }

    private function getProductByCategorySlug($slug, $limit)
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        return DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('product_sales', function ($join) use ($now) {
                $join->on('product_sales.product_id', '=', 'products.id')
                    ->where('product_sales.status', 1)
                    ->where('product_sales.date_begin', '<=', $now)
                    ->where('product_sales.date_end', '>=', $now);
            })
            ->select('products.*', 'product_sales.price_sale')
            ->where('categories.slug', $slug)
            ->where('products.status', 1)
            ->limit($limit)
            ->get();
    }

    private function getProductByCriteria($type, $limit)
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        return DB::table('products')
            ->leftJoin('product_sales', function ($join) use ($now) {
                $join->on('product_sales.product_id', '=', 'products.id')
                    ->where('product_sales.status', 1)
                    ->where('product_sales.date_begin', '<=', $now)
                    ->where('product_sales.date_end', '>=', $now);
            })
            ->select('products.*', 'product_sales.price_sale')
            ->where('products.status', 1)
            ->orderByDesc('products.id')
            ->limit($limit)
            ->get();
    }

    // =================================================
    // 📌 LẤY SẢN PHẨM THEO DANH MỤC (TRANG LIST)
    // =================================================
    public function byCategory($slug)
    {
        try {
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $products = DB::table('products')
                ->join('categories', 'categories.id', '=', 'products.category_id')
                ->leftJoin('product_stores', 'product_stores.product_id', '=', 'products.id')
                ->leftJoin('product_sales', function ($join) use ($now) {
                    $join->on('product_sales.product_id', '=', 'products.id')
                        ->where('product_sales.status', 1)
                        ->where('product_sales.date_begin', '<=', $now)
                        ->where('product_sales.date_end', '>=', $now);
                })
                ->select(
                    'products.id',
                    'products.name',
                    'products.slug',
                    'products.thumbnail',
                    'products.description',
                    'products.price_buy',
                    'product_sales.price_sale', 
                    'categories.name as category_name',
                    DB::raw('IFNULL(product_stores.qty, 0) as stock')
                )
                ->where('categories.slug', $slug)
                ->where('products.status', 1)
                ->orderByDesc('products.id')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $products
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =================================================
    // 📌 SẢN PHẨM MỚI (RIÊNG LẺ NẾU CẦN)
    // =================================================
    public function productNew(Request $request)
    {
        $limit = $request->limit ?? 10;
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        $products = DB::table('products')
            ->join('product_stores', 'product_stores.product_id', '=', 'products.id')
            ->leftJoin('product_sales', function ($join) use ($now) {
                $join->on('product_sales.product_id', '=', 'products.id')
                    ->where('product_sales.status', 1)
                    ->where('product_sales.date_begin', '<=', $now)
                    ->where('product_sales.date_end', '>=', $now);
            })
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.thumbnail',
                'products.price_buy',
                'product_sales.price_sale',
                'product_stores.qty as stock'
            )
            ->where('products.status', 1)
            ->where('product_stores.qty', '>', 0)
            ->orderByDesc('products.id')
            ->limit($limit)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $products
        ]);
    }
}