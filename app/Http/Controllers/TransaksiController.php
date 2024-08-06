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
        // Validate the incoming request
        $request->validate([
            'kode' => 'required',
            'tgl' => 'required|date',
            'cust_id' => 'required',
            'subtotal' => 'required|numeric',
            'diskon' => 'required|numeric',
            'ongkir' => 'nullable|numeric',
            'total_bayar' => 'required|numeric',
            'cartItems' => 'required|array',
            'cartItems.*.barang_id' => 'required',
            'cartItems.*.diskon_nilai' => 'required|numeric',
            'cartItems.*.diskon_pct' => 'required|numeric',
            'cartItems.*.harga_bandrol' => 'required|numeric',
            'cartItems.*.harga_diskon' => 'required|numeric',
            'cartItems.*.qty' => 'required|integer',
            'cartItems.*.total' => 'required|numeric',
        ]);

        // Create the transaction
        $transaction = T_sales::create([
            'kode' => $request->kode,
            'tgl' => $request->tgl,
            'cust_id' => $request->cust_id,
            'subtotal' => $request->subtotal,
            'diskon' => $request->diskon,
            'ongkir' => $request->ongkir ?? 0,
            'total_bayar' => $request->total_bayar,
        ]);

        // Create the transaction items
        foreach ($request->cartItems as $item) {
            T_sales_det::create([
                'sales_id' => T_sales::max('id') + 1,
                'barang_id' => $item['barang_id'],
                'harga_bandrol' => $item['harga_bandrol'],
                'qty' => $item['qty'],
                'diskon_pct' => $item['diskon_pct'],
                'diskon_nilai' => $item['diskon_nilai'],
                'harga_diskon' => $item['harga_diskon'],
                'total' => $item['total'],
            ]);
        }

        return response()->json(['message' => 'Transaction created successfully'], 201);
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
