<?php
use Illuminate\Support\Facades\Gate;

class ImportController
{
    public function import(Request $request)
    {
        Gate::authorize('inventory-manage');

                    // xử lý nhập kho
    }
}
