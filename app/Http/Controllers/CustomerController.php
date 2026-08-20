<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('transactions');

        if ($request->search) {
            $q = $request->search;
            $query->where(function($qr) use ($q) {
                $qr->where('name', 'like', "%$q%")
                   ->orWhere('phone', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            });
        }

        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Customer::create($request->only('name', 'phone', 'email', 'address', 'notes'));
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $customer = Customer::with(['transactions' => function($q) {
            $q->latest()->limit(10);
        }])->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        Customer::findOrFail($id)->update($request->only('name', 'phone', 'email', 'address', 'notes'));
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();
        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil dihapus.');
    }
}
