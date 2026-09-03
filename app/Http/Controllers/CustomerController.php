<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('contact_number', 'like', "%{$s}%")
                  ->orWhere('plate_number', 'like', "%{$s}%")
                  ->orWhere('vehicle_make_model', 'like', "%{$s}%");
            });
        }

        $customers = $query->latest()->paginate(12)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function orders(Request $request, Customer $customer)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::with(['items', 'user'])
            ->where('customer_id', $customer->id);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->latest()->get();
        $totalSpent = $orders->sum('total_amount');

        return response()->json([
            'customer' => $customer,
            'orders' => $orders,
            'total_spent' => $totalSpent,
            'count' => $orders->count(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'vehicle_make_model' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', 'Customer record added successfully!');
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'vehicle_make_model' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Customer record updated successfully!');
    }

    public function destroy(Customer $customer)
    {
        $name = $customer->name;
        $customer->delete();
        return redirect()->route('customers.index')->with('success', "Customer record for '{$name}' deleted.");
    }
}
