<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use App\Models\InventoryHistory;

Class HistoryController extends Controller
{
    public function history()
{
    try {
        $data = InventoryHistory::with('product')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}
}
