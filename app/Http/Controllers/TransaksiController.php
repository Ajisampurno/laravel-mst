<?php

namespace App\Http\Controllers;

use App\Models\M_barang;
use App\Models\M_customer;
use App\Models\T_sales;
use App\Models\T_sales_det;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = T_sales::select(
            't_sales.id',
            'tgl',
            'm_customers.nama',
            't_sales_dets.qty',
            't_sales.subtotal',
            't_sales.diskon',
            't_sales.ongkir',
            't_sales.total_bayar'
        )
            ->join('m_customers', 'm_customers.id', '=', 't_sales.cust_id')
            ->join('t_sales_dets', 't_sales_dets.sales_id', '=', 't_sales.id')
            ->get();

        $grandTotal = $transaksis->sum('total_bayar');

        $customers = M_customer::select('id', 'kode', 'nama', 'telp')->get();

        $barangs = M_barang::all();

        return response()->json([
            'transaksis' => $transaksis,
            'grandTotal' => $grandTotal,
            'customers' => $customers,
            'barangs' => $barangs
        ]);
    }

    public function store(Request $request)
    {
        return response()->json($request);
        // Validasi data input
        $request->validate([
            'kode' => 'required|string|max:255',
            'tgl' => 'required|date',
            'cust_id' => 'required|exists:m_customers,id',
            'subtotal' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'ongkir' => 'nullable|numeric',
            'total_bayar' => 'required|numeric',
            'details' => 'required|array',
            'details.*.barang_id' => 'required',
            'details.*.harga_bandrol' => 'required|numeric',
            'details.*.qty' => 'required|integer',
            'details.*.diskon_pct' => 'nullable|numeric',
            'details.*.diskon_nilai' => 'nullable|numeric',
            'details.*.harga_diskon' => 'required|numeric',
            'details.*.total' => 'required|numeric',
        ]);

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Simpan data transaksi ke tabel t_sales
            $transaksi = T_sales::create([
                'kode' => $request->kode,
                'tgl' => $request->tgl,
                'cust_id' => $request->cust_id,
                'subtotal' => $request->subtotal,
                'diskon' => $request->diskon,
                'ongkir' => $request->ongkir,
                'total_bayar' => $request->total_bayar,
            ]);

            // Simpan data detail transaksi ke tabel t_sales_dets
            foreach ($request->details as $detail) {
                T_sales_det::create([
                    'sales_id' => $transaksi->id,
                    'barang_id' => $detail['barang_id'],
                    'harga_bandrol' => $detail['harga_bandrol'],
                    'qty' => $detail['qty'],
                    'diskon_pct' => $detail['diskon_pct'],
                    'diskon_nilai' => $detail['diskon_nilai'],
                    'harga_diskon' => $detail['harga_diskon'],
                    'total' => $detail['total'],
                ]);
            }

            // Commit transaksi database
            DB::commit();

            return response()->json(['message' => 'Transaksi berhasil disimpan'], 201);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $sale = T_sales::find($id);

            if (!$sale) {
                return response()->json(['message' => 'Data not found'], 404);
            }

            T_sales_det::where('sales_id', $id)->delete();
            $sale->delete();

            DB::commit();

            return response()->json(['message' => 'Data deleted successfully'], 200);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json(['message' => 'Failed to delete data', 'error' => $e->getMessage()], 500);
        }
    }
}
