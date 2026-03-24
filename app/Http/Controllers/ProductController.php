<?php

namespace App\Http\Controllers;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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

    // Lấy tên bảng thực tế từ Model để tránh viết sai tiền tố
    $productTable = (new Product())->getTable(); // sẽ ra 'ntc_products'
    $saleTable = (new ProductSale())->getTable(); // sẽ ra 'ntc_product_sales'

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

    // Lọc theo từ khóa
    if ($request->filled('search')) {
        $query->where("$productTable.name", 'like', '%' . $request->search . '%');
    }

    // Lọc theo danh mục
    if ($request->filled('category_id')) {
        $query->where("$productTable.category_id", $request->category_id);
    }

    $products = $query->orderBy("$productTable.id", 'asc')->get();

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
                ->join('categories', 'categories.id', '=', 'products.category_id') // Thêm join category
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
                    'categories.name as category_name', // Để Frontend chia Tab Sale
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
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // =================================================
    // 📌 THÊM SẢN PHẨM MỚI
    // =================================================
   public function store(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
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

            // UPLOAD LÊN CLOUDINARY
            if ($request->hasFile('thumbnail')) {
                $result = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'cameraclick/products'
                ]);
                // Lấy link tuyệt đối lưu vào DB
                $product->thumbnail = $result->getSecurePath(); 
            }

            $product->save();

            ProductStore::create([
                'product_id' => $product->id,
                'qty' => $validated['stock'],
                'price_root' => $validated['price_buy'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thêm sản phẩm thành công (Lưu Cloudinary)',
                'data' => $product
            ], 201);
        });
    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
    }
}

    // =================================================
    // 📌 CHI TIẾT SẢN PHẨM (KÈM GIÁ SALE)
    // =================================================
    public function show($id)
    {
        try {
            $now = Carbon::now('Asia/Ho_Chi_Minh');

            // Bước 1: Thử lấy sản phẩm cơ bản trước (không load quan hệ để test)
            $product = Product::where('id', $id)
                ->orWhere('slug', $id)
                ->first();

            if (!$product) {
                return response()->json(['status' => false, 'message' => 'Không tìm thấy sản phẩm này trong Database Railway'], 404);
            }

            // Bước 2: Thử load từng quan hệ một để xem cái nào làm sập Server
            try {
                $product->load(['category', 'product_store']);
                // Nếu bạn có bảng ntc_product_images và ntc_product_attributes thì mới load dòng dưới
                $product->load(['images', 'attributes']); 
            } catch (\Exception $e_relation) {
                return response()->json([
                    'status' => false, 
                    'message' => 'Lỗi ở phần Liên kết (Relationships): ' . $e_relation->getMessage()
                ], 500);
            }

            // Bước 3: Quét tìm giá sale
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
            // TRẢ VỀ LỖI CHI TIẾT ĐỂ CÔNG ĐỌC ĐƯỢC LUÔN
            return response()->json([
                'status' => false,
                'error_type' => get_class($e),
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // =================================================
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Sản phẩm không tồn tại'], 404);
        }

        try {
            return DB::transaction(function () use ($request, $product) {
                // 1. Xử lý hình ảnh (Thumbnail)
                if ($request->hasFile('thumbnail')) {
                    // Xóa file cũ nếu tồn tại
                    if ($product->thumbnail) {
                        $oldPath = str_replace('storage/', '', $product->thumbnail);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    // Lưu file mới
                    $path = $request->file('thumbnail')->store('uploads/products', 'public');
                    $product->thumbnail = 'storage/' . $path;
                }

                // 2. Cập nhật thông tin cơ bản
                // Fill ngoại trừ các trường đặc biệt để xử lý riêng
                $product->fill($request->except(['thumbnail', 'attributes', 'stock', '_method']));

                if ($request->filled('name')) {
                    $product->slug = Str::slug($request->name) . '-' . $product->id;
                }

                $product->save();

                // 3. Cập nhật kho hàng (Stock)
                if ($request->has('stock')) {
                    ProductStore::updateOrCreate(
                        ['product_id' => $product->id, 'type' => null],
                        [
                            'qty' => $request->stock,
                            'price_root' => $request->price_buy ?? ($product->price_buy ?? 0)
                        ]
                    );
                }

                // 4. Cập nhật thuộc tính (Attributes - Mối quan hệ n-n)
                if ($request->has('attributes')) {
                    $syncData = [];
                    // Đảm bảo attributes được gửi lên là mảng
                    $attributes = $request->input('attributes', []);
                    if (is_string($attributes)) {
                        $attributes = json_decode($attributes, true);
                    }

                    foreach ($attributes as $attr) {
                        if (!empty($attr['attribute_id']) && isset($attr['value'])) {
                            $syncData[$attr['attribute_id']] = ['value' => $attr['value']];
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
        } catch (\Exception $e) {
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

    // Không cần xóa Storage local nữa vì mình dùng online
    ProductStore::where('product_id', $id)->delete();
    ProductSale::where('product_id', $id)->delete();
    $product->delete();

    return response()->json(['status' => true, 'message' => 'Xóa sản phẩm thành công']);
}

    // =================================================
    // 📌 LẤY DATA TRANG HOME (Mới, Camera, Lens)
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

    // Helper: Lấy sản phẩm theo danh mục có kèm giá Sale
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

    // Helper: Lấy sản phẩm mới nhất có kèm giá Sale
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
                // Join bảng sale để lấy giá khuyến mãi
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
                    'product_sales.price_sale', // Giá sale (nếu có)
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