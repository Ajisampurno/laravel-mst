<?php

namespace App\Http\Controllers;

use App\Models\M_barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        return M_barang::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'harga' => 'required',
        ]);

        $barang = M_barang::create($request->all());
        return response()->json($barang, 201);
    }

    public function show($id)
    {
        $barang = M_barang::find($id);

        if ($barang) {
            return response()->json($barang);
        }

        return response()->json(['message' => 'Barang not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $barang = M_barang::find($id);

        if ($barang) {
            $request->validate([
                'nama' => 'sometimes|required',
                'harga' => 'sometimes|required',
            ]);

            $barang->update($request->all());
            return response()->json($barang);
        }

        return response()->json(['message' => 'Barang not found'], 404);
    }

    public function destroy($id)
    {
        $barang = M_barang::find($id);

        if ($barang) {
            $barang->delete();
            return response()->json(['message' => 'Barang deleted successfully']);
        }

        return response()->json(['message' => 'Barang not found'], 404);
    }
}
