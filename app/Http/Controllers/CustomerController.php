<?php
namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller {
    public function index() {
        $customers = Customer::all();
        return view('customer.index', compact('customers'));
    }
    public function create() {
        return view('customer.create');
    }
    public function store(Request $request) {
        $request->validate([
            'nama' => 'required', 'no_telp' => 'required',
        ]);
        Customer::create($request->all());
        return redirect()->route('customer.index')->with('success', 'Customer ditambahkan');
    }
    public function edit($id) {
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }
    public function update(Request $request, $id) {
        $customer = Customer::findOrFail($id);
        $customer->update($request->all());
        return redirect()->route('customer.index')->with('success', 'Customer diupdate');
    }
    public function destroy($id) {
        Customer::findOrFail($id)->delete();
        return redirect()->route('customer.index')->with('success', 'Customer dihapus');
    }
}