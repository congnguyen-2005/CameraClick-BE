<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateExpiredSalePrice extends Command
{
    // Tên lệnh: php artisan sale:update-expired
    protected $signature = 'sale:update-expired';
    protected $description = 'Chốt giá sale thành giá gốc khi hết hạn';

    public function handle()
    {
        // Lấy giờ hiện tại theo múi giờ VN
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        
        $this->info("--- Đang quét bảng ntc_product_sales lúc: $now ---");

        // LƯU Ý: Nếu trong .env có DB_PREFIX=ntc_ thì chỉ để 'product_sales'
        // Nếu không có prefix thì điền thẳng 'ntc_product_sales'
        $tableName = 'product_sales'; 

        $expiredSales = DB::table($tableName)
            ->where('date_end', '<=', $now)
            ->where('status', 1) // Chỉ quét những đợt sale đang Active
            ->get();

        if ($expiredSales->isEmpty()) {
            $this->comment("Không có sản phẩm nào đang Active (status=1) và hết hạn.");
            return;
        }

        foreach ($expiredSales as $sale) {
            DB::beginTransaction();
            try {
                // 1. Cập nhật giá price_buy trong bảng products
                DB::table('products')
                    ->where('id', $sale->product_id)
                    ->update([
                        'price_buy' => $sale->price_sale,
                        'updated_at' => now()
                    ]);

                // 2. Chuyển status về 0 để đánh dấu đã xử lý xong
                DB::table($tableName)
                    ->where('id', $sale->id)
                    ->update([
                        'status' => 0,
                        'updated_at' => now()
                    ]);

                DB::commit();
                $this->info("✔ Thành công: SP ID {$sale->product_id} đã nhận giá mới " . number_format($sale->price_sale) . "₫");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✘ Lỗi SP ID {$sale->product_id}: " . $e->getMessage());
            }
        }
    }
}