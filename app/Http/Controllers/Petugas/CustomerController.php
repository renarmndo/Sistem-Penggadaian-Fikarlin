<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        return view('petugas.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('petugas.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identity_number' => ['required', 'string', 'max:30', 'unique:customers,identity_number'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer = Customer::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $customer,
            ]);
        }

        return redirect()->route('petugas.customers.index')->with('success', 'Master data nasabah berhasil ditambahkan.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['pawnTransactions.item', 'purchaseTransactions.item']);
        return view('petugas.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('petugas.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'identity_number' => ['required', 'string', 'max:30', 'unique:customers,identity_number,' . $customer->id],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $customer->update($validated);

        return redirect()->route('petugas.customers.index')->with('success', 'Data nasabah berhasil diperbarui.');
    }

    public function searchJson(Request $request)
    {
        $search = $request->input('q');
        $customers = Customer::search($search)->take(10)->get();

        return response()->json($customers);
    }
}
