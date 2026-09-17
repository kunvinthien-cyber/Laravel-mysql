<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $customers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'nullable|max:255',
            'email'   => 'nullable|email|unique:customers,email',
            'phone'   => 'nullable|max:30',
            'address' => 'nullable|max:500',
            'points'  => 'nullable|integer|min:0',
            'debt'    => 'nullable|numeric|min:0',
        ]);

        $customerData = [
            'name' => trim((string) $request->input('name', '')) ?: 'Walk-in customer',
            'email' => trim((string) $request->input('email', '')) ?: null,
            'phone' => trim((string) $request->input('phone', '')) ?: null,
            'address' => trim((string) $request->input('address', '')) ?: null,
            'points' => $request->input('points', 0) ?? 0,
            'debt' => $request->input('debt', 0) ?? 0,
        ];

        $customer = Customer::create($customerData);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customer_id' => $customer->id,
                'message' => 'Customer created successfully.'
            ]);
        }

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'    => 'nullable|max:255',
            'email'   => 'nullable|email|unique:customers,email,' . $customer->id,
            'phone'   => 'nullable|max:30',
            'address' => 'nullable|max:500',
            'points'  => 'nullable|integer|min:0',
            'debt'    => 'nullable|numeric|min:0',
        ]);

        $customer->update([
            'name' => trim((string) $request->input('name', '')) ?: $customer->name ?: 'Walk-in customer',
            'email' => trim((string) $request->input('email', '')) ?: null,
            'phone' => trim((string) $request->input('phone', '')) ?: null,
            'address' => trim((string) $request->input('address', '')) ?: null,
            'points' => $request->input('points', $customer->points) ?? $customer->points,
            'debt' => $request->input('debt', $customer->debt) ?? $customer->debt,
        ]);

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // ឆែកសិទ្ធិ៖ ប្រសិនបើជា Cashier (ឬ 'user') គឺមិនអនុញ្ញាតឱ្យលុបឡើយ
        if (auth()->user()->isCashier()) {
            abort(403, 'Unauthorized action. Cashiers are not allowed to delete customers.');
        }

        $customer->delete();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
