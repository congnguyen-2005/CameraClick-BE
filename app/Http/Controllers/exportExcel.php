<?php
namespace App\Http\Controllers;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
Class ExportExcelController extends Controller
{
    public function exportExcel()
{
    Gate::authorize('inventory-manage');
    return Excel::download(new InventoryExport, 'ton-kho.xlsx');
}

}
