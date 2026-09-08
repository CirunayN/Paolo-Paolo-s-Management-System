<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderPayment;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::withCount('orders')
            ->with(['orders' => function($q) {
                $q->where('balance_due', '>', 0);
            }]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('contact_number', 'like', "%{$s}%")
                  ->orWhere('plate_number', 'like', "%{$s}%")
                  ->orWhere('vehicle_make_model', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%");
            });
        }

        if ($request->input('filter') === 'installments') {
            $query->whereHas('orders', function($q) {
                $q->where('balance_due', '>', 0);
            });
        }

        $customers = $query->latest()->paginate(12)->withQueryString();

        // High level KPI stats
        $totalCustomersCount = Customer::count();
        $activeInstallmentsCount = Order::where('balance_due', '>', 0)->count();
        $totalReceivables = (float) Order::where('balance_due', '>', 0)->sum('balance_due');

        return view('customers.index', compact('customers', 'totalCustomersCount', 'activeInstallmentsCount', 'totalReceivables'));
    }

    public function orders(Request $request, Customer $customer)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Order::with(['items', 'user', 'payments'])
            ->where('customer_id', $customer->id);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->latest()->get();
        $totalSpent = $orders->sum('total_amount');
        $totalBalanceDue = (float) $orders->sum('balance_due');

        return response()->json([
            'customer' => $customer,
            'orders' => $orders,
            'total_spent' => $totalSpent,
            'total_balance_due' => $totalBalanceDue,
            'count' => $orders->count(),
        ]);
    }

    public function activeInstallments(Customer $customer)
    {
        $orders = Order::where('customer_id', $customer->id)
            ->where('balance_due', '>', 0)
            ->with(['items', 'payments'])
            ->latest()
            ->get();

        return response()->json([
            'customer' => $customer,
            'orders' => $orders,
            'total_balance' => (float) $orders->sum('balance_due'),
        ]);
    }

    public function collectPayment(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:Cash,GCash / Maya,Card,Bank Transfer',
            'payment_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255',
        ]);

        $order = Order::where('customer_id', $customer->id)->findOrFail($validated['order_id']);

        if ($order->balance_due <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This order is already fully paid!'
            ], 422);
        }

        $amountToPay = floatval($validated['amount']);
        if ($amountToPay > $order->balance_due) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed remaining balance due of ₱' . number_format($order->balance_due, 2)
            ], 422);
        }

        return DB::transaction(function () use ($order, $customer, $validated, $amountToPay) {
            $nextPaymentNumber = $order->payments()->count() + 1;

            $payment = OrderPayment::create([
                'order_id' => $order->id,
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'payment_number' => $nextPaymentNumber,
                'amount' => $amountToPay,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'notes' => $validated['notes'] ?? ("Installment payment #{$nextPaymentNumber}"),
                'payment_date' => now(),
            ]);

            $newAmountPaid = $order->amount_paid + $amountToPay;
            $newBalanceDue = max(0, $order->balance_due - $amountToPay);
            $newStatus = ($newBalanceDue <= 0) ? 'Paid' : 'Partial';

            $order->update([
                'amount_paid' => $newAmountPaid,
                'balance_due' => $newBalanceDue,
                'payment_status' => $newStatus,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Payment of ₱" . number_format($amountToPay, 2) . " recorded successfully!",
                'order' => $order->fresh()->load('payments'),
                'payment' => $payment,
                'new_balance' => $newBalanceDue,
                'receipt_url' => route('pos.receipt', $order->id),
            ]);
        });
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
        return redirect()->route('customers.index')->with('success', "Customer record for '{$name}' moved to Trash / Archive. Historical orders remain intact.");
    }

    public function restore($id)
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();
        return redirect()->route('customers.index', ['archived' => 1])
            ->with('success', "Customer record for '{$customer->name}' has been restored successfully.");
    }
}
