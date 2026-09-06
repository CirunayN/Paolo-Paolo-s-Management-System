<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Inventory;
use App\Models\StockTransaction;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'inventory'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $vehicleBrands = Product::whereNotNull('vehicle_brand')
            ->where('vehicle_brand', '!=', '')
            ->distinct()
            ->pluck('vehicle_brand');

        $customers = Customer::orderBy('name')->get();

        return view('pos.index', compact('products', 'categories', 'vehicleBrands', 'customers'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|numeric|min:0.1',
            'cart.*.price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:30',
            'vehicle_details' => 'nullable|string|max:150',
            'order_type' => 'required|string|in:Walk-in,With Installation,Pick-up / Delivery',
            'installation_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,GCash / Maya,Card,Bank Transfer',
            'payment_reference' => 'nullable|string|max:100',
            'amount_tendered' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();

            // Handle Customer (Auto-register unregistered customer into Customers module)
            $customerId = $validated['customer_id'] ?? null;
            $customerName = trim($validated['customer_name'] ?? '');
            $customerPhone = trim($request->input('customer_phone') ?? '');
            $vehicleModel = trim($request->input('vehicle_model') ?? '');
            $plateNumber = trim($request->input('plate_number') ?? '');

            if (!$customerId && $customerName && strcasecmp($customerName, 'Walk-in Customer') !== 0) {
                $query = Customer::where('name', $customerName);
                if ($customerPhone) {
                    $query->orWhere('contact_number', $customerPhone);
                }
                $existingCust = $query->first();

                if (!$existingCust) {
                    $existingCust = Customer::create([
                        'name' => $customerName,
                        'contact_number' => $customerPhone ?: null,
                        'vehicle_make_model' => $vehicleModel ?: null,
                        'plate_number' => $plateNumber ?: null,
                        'address' => 'Davao City',
                    ]);
                } else {
                    if ($vehicleModel && !$existingCust->vehicle_make_model) {
                        $existingCust->vehicle_make_model = $vehicleModel;
                    }
                    if ($plateNumber && !$existingCust->plate_number) {
                        $existingCust->plate_number = $plateNumber;
                    }
                    if ($customerPhone && !$existingCust->contact_number) {
                        $existingCust->contact_number = $customerPhone;
                    }
                    $existingCust->save();
                }
                $customerId = $existingCust->id;
            } elseif ($customerId) {
                // If existing customer selected, update empty fields if newly supplied
                $existingCust = Customer::find($customerId);
                if ($existingCust) {
                    $updated = false;
                    if ($customerPhone && $existingCust->contact_number !== $customerPhone) {
                        $existingCust->contact_number = $customerPhone;
                        $updated = true;
                    }
                    if ($vehicleModel && $existingCust->vehicle_make_model !== $vehicleModel) {
                        $existingCust->vehicle_make_model = $vehicleModel;
                        $updated = true;
                    }
                    if ($plateNumber && $existingCust->plate_number !== $plateNumber) {
                        $existingCust->plate_number = $plateNumber;
                        $updated = true;
                    }
                    if ($updated) {
                        $existingCust->save();
                    }
                }
            }

            // Calculate Subtotal
            $subtotal = 0;
            foreach ($validated['cart'] as $item) {
                $subtotal += ($item['quantity'] * $item['price']);
            }

            $installationFee = floatval($validated['installation_fee'] ?? 0);
            $discount = floatval($validated['discount_amount'] ?? 0);
            $totalAmount = max(0, $subtotal + $installationFee - $discount);
            $tendered = floatval($validated['amount_tendered']);

            if ($tendered < $totalAmount && in_array($validated['payment_method'], ['Cash'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tendered amount ₱' . number_format($tendered, 2) . ' is less than total amount ₱' . number_format($totalAmount, 2)
                ], 422);
            }

            $change = max(0, $tendered - $totalAmount);

            // Generate unique invoice number: INV-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $latestOrder = Order::whereDate('created_at', today())->latest()->first();
            $sequence = $latestOrder ? (intval(substr($latestOrder->invoice_no, -4)) + 1) : 1;
            $invoiceNo = 'INV-' . $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $finalVehicleDetails = $validated['vehicle_details'] ?? null;
            if (!$finalVehicleDetails && ($vehicleModel || $plateNumber)) {
                $finalVehicleDetails = trim($vehicleModel . ($plateNumber ? " ({$plateNumber})" : ''));
            }

            $finalCustName = $customerName ?: ($customerId ? Customer::find($customerId)?->name : 'Walk-in Customer');

            // Create Order
            $order = Order::create([
                'invoice_no' => $invoiceNo,
                'user_id' => $user->id,
                'customer_id' => $customerId,
                'customer_name' => $finalCustName,
                'vehicle_details' => $finalVehicleDetails,
                'order_type' => $validated['order_type'],
                'subtotal' => $subtotal,
                'installation_fee' => $installationFee,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'amount_tendered' => $tendered,
                'change_amount' => $change,
                'payment_status' => 'Paid',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create Order Items and Update Inventory
            foreach ($validated['cart'] as $item) {
                $product = Product::with('inventory')->findOrFail($item['id']);
                $itemSubtotal = $item['quantity'] * $item['price'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $itemSubtotal,
                ]);

                // Deduct stock if inventory tracking applies (skip purely labor services)
                if ($product->inventory && $product->category->slug !== 'installation-services') {
                    $inv = $product->inventory;
                    $inv->quantity_on_hand = max(0, $inv->quantity_on_hand - $item['quantity']);
                    $inv->save();

                    StockTransaction::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'pos_sale',
                        'quantity' => -$item['quantity'],
                        'balance_after' => $inv->quantity_on_hand,
                        'reference_no' => $order->invoice_no,
                        'remarks' => "POS Order: {$order->invoice_no}",
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale transaction completed successfully!',
                'order' => $order->load(['items', 'user', 'customer']),
                'receipt_url' => route('pos.receipt', $order->id),
            ]);
        });
    }

    public function receipt(Order $order)
    {
        $order->load(['items', 'user', 'customer']);
        return view('pos.receipt', compact('order'));
    }
}
