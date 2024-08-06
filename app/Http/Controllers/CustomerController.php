<?php

namespace App\Http\Controllers;

use App\Models\M_customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Get all customers
    public function index()
    {
        return M_customer::all();
    }

    // Store a new customer
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'telp' => 'required',
        ]);

        $customer = M_customer::create($request->all());
        return response()->json($customer, 201);
    }

    // Show a specific customer
    public function show($id)
    {
        $customer = M_customer::find($id);

        if ($customer) {
            return response()->json($customer);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }

    // Update a customer
    public function update(Request $request, $id)
    {
        $customer = M_customer::find($id);

        if ($customer) {
            $request->validate([
                'nama' => 'sometimes|required',
                'telp' => 'sometimes|required',
            ]);

            $customer->update($request->all());
            return response()->json($customer);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }

    // Delete a customer
    public function destroy($id)
    {
        $customer = M_customer::find($id);

        if ($customer) {
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully']);
        }

        return response()->json(['message' => 'Customer not found'], 404);
    }
}
