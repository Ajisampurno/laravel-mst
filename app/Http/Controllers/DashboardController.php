<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\T_sales;
use App\Models\T_sales_det;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDonutChart()
    {
        $totalSales = T_sales_det::sum('qty');
        $salesByProduct = T_sales_det::select('barang_id', 'm_barangs.nama as nama', DB::raw('SUM(qty) as total_quantity'))
            ->join('m_barangs', 'm_barangs.id', '=', 'T_sales_dets.barang_id')
            ->groupBy('barang_id')
            ->get();

        $salesPercentage = $salesByProduct->map(function ($sale) use ($totalSales) {
            return [
                'nama' => $sale->nama,
                'percentage' => ($totalSales > 0) ? ($sale->total_quantity / $totalSales) * 100 : 0
            ];
        });

        return response()->json($salesPercentage);
    }

    public function getBarChart()
    {
        $sales = T_sales::select(
            DB::raw('DATE(tgl) as tanggal'),
            DB::raw('SUM(total_bayar) as total_penjualan')
        )
            ->groupBy('tanggal')
            ->get();

        $totalSales = $sales->sum('total_penjualan');

        $data = $sales->map(function ($item) use ($totalSales) {
            return [
                'tanggal' => Carbon::parse($item->tanggal)->format('Y-m-d'),
                'percentage' => $totalSales > 0 ? round(($item->total_penjualan / $totalSales) * 100, 2) : 0
            ];
        });

        return response()->json($data);
    }

    public function totalToday()
    {
        $today = Carbon::today();

        $totalTransactions = T_sales::whereDate('created_at', $today)
            ->sum('total_bayar');

        return response()->json([
            'total_transactions' => $totalTransactions,
        ]);
    }
}
