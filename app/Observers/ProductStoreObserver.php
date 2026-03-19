<?php

namespace App\Observers;
use App\Models\Product;

use App\Models\ProductStore;

class ProductStoreObserver
{
    public function saved(ProductStore $store)
    {
        // Chỉ xử lý khi thay đổi bản ghi gốc (type = null)
        if ($store->type === null) {
            $product = Product::find($store->product_id);
            if ($product) {
                $product->stock = $store->qty;
                $product->saveQuietly(); // Lưu mà không kích hoạt event khác để tránh vòng lặp
            }
        }
    }
    /**
     * Handle the ProductStore "created" event.
     */
    public function created(ProductStore $productStore): void
    {
        //
    }

    /**
     * Handle the ProductStore "updated" event.
     */
    public function updated(ProductStore $productStore): void
    {
        //
    }

    /**
     * Handle the ProductStore "deleted" event.
     */
    public function deleted(ProductStore $productStore): void
    {
        //
    }

    /**
     * Handle the ProductStore "restored" event.
     */
    public function restored(ProductStore $productStore): void
    {
        //
    }

    /**
     * Handle the ProductStore "force deleted" event.
     */
    public function forceDeleted(ProductStore $productStore): void
    {
        //
    }
}
