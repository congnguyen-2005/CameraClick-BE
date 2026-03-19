<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // ✅ BẮT BUỘC
use App\Models\ProductStore;
use App\Observers\ProductStoreObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('inventory-manage', function ($user) {
            return $user->role === 'admin';
        });
         ProductStore::observe(ProductStoreObserver::class);
    }
   
}
