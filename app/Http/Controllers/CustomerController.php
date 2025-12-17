<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('customer.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        Customer::create($request->only(['nama','no_telp','alamat']));

        return redirect()->back()->with('success', 'Customer baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only(['nama','no_telp','alamat']));

        return redirect()->back()->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        
        // Opsional: Cek dulu apakah customer ini pernah belanja? 
        // Kalau pernah, sebaiknya jangan dihapus hard-delete (bisa error di report penjualan), 
        // tapi untuk sekarang kita pakai standar hapus saja.
        $customer->delete();

        return redirect()->back()->with('success', 'Customer berhasil dihapus.');
    }
}