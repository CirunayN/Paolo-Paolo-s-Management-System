<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockTransaction;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\OrderItem;

class Sep4AndSep6Seeder extends Seeder
{
    /**
     * Run the database seeds for September 4, 2026 and September 6, 2026.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $cashier = User::where('role', 'cashier')->first() ?? $admin;

        // 1. Stock-In Receiving on September 4, 2026
        $supplier = Supplier::first();
        $sep4Morning = Carbon::create(2026, 9, 4, 8, 30, 0);

        $stockIn = StockIn::firstOrCreate(
            ['reference_no' => 'STK-2026-0904-01'],
            [
                'supplier_id' => $supplier ? $supplier->id : null,
                'user_id' => $admin->id,
                'total_cost' => 0,
                'notes' => 'Pre-weekend inventory replenishment: Fortuner, Hilux, and detailing care kits',
                'received_date' => '2026-09-04',
                'created_at' => $sep4Morning,
                'updated_at' => $sep4Morning,
            ]
        );

        $restockProducts = Product::whereIn('product_code', ['MAT-TY-FORT', 'MAT-TY-HLX', 'ACC-CARE-MCR'])->get();
        $totalShipmentCost = 0;
        foreach ($restockProducts as $rProd) {
            $qty = 15;
            $sub = $qty * $rProd->cost_price;
            $totalShipmentCost += $sub;

            StockInItem::firstOrCreate(
                ['stock_in_id' => $stockIn->id, 'product_id' => $rProd->id],
                [
                    'quantity_received' => $qty,
                    'cost_per_unit' => $rProd->cost_price,
                    'subtotal' => $sub,
                    'created_at' => $sep4Morning,
                    'updated_at' => $sep4Morning,
                ]
            );

            // Add to physical inventory
            $inv = Inventory::firstOrCreate(
                ['product_id' => $rProd->id],
                ['quantity_on_hand' => 0, 'reorder_level' => 3]
            );
            $inv->quantity_on_hand += $qty;
            $inv->last_restocked_at = $sep4Morning;
            $inv->save();

            StockTransaction::create([
                'product_id' => $rProd->id,
                'user_id' => $admin->id,
                'type' => 'stock_in',
                'quantity' => $qty,
                'balance_after' => $inv->quantity_on_hand,
                'reference_no' => 'STK-2026-0904-01',
                'remarks' => "Stock-In Receiving from {$supplier?->supplier_name}",
                'created_at' => $sep4Morning,
                'updated_at' => $sep4Morning,
            ]);
        }

        $stockIn->total_cost = $totalShipmentCost;
        $stockIn->save();

        // 2. Orders definitions for September 4 and September 6
        $ordersDefinition = [
            // === SEPTEMBER 4, 2026 ===
            [
                'datetime' => Carbon::create(2026, 9, 4, 9, 42, 15),
                'invoice_no' => 'INV-20260904-1001',
                'customer_search' => 'Juan Dela Cruz',
                'fallback_name' => 'Juan Dela Cruz',
                'vehicle_details' => 'Toyota Fortuner 2023 (NBH-4821)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'GCash / Maya',
                'payment_reference' => 'GC-20260904-77182',
                'tendered' => 5550.00,
                'notes' => 'Installed with OEM retention hooks and rear trunk tray fitted',
                'items' => [
                    ['code' => 'MAT-TY-FORT', 'qty' => 1],
                    ['code' => 'ACC-TRK-FORT', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 4, 11, 15, 30),
                'invoice_no' => 'INV-20260904-1002',
                'customer_search' => null,
                'fallback_name' => 'Walk-in Customer (Yamaha Aerox)',
                'vehicle_details' => 'Yamaha NMAX / Aerox',
                'order_type' => 'Walk-in',
                'installation_fee' => 0.00,
                'discount' => 0.00,
                'payment_method' => 'Cash',
                'payment_reference' => null,
                'tendered' => 1000.00,
                'notes' => 'Cash counter sale',
                'items' => [
                    ['code' => 'MAT-MC-NMAX', 'qty' => 1],
                    ['code' => 'ACC-CARE-MCR', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 4, 14, 8, 45),
                'invoice_no' => 'INV-20260904-1003',
                'customer_search' => 'Bryan Tan',
                'fallback_name' => 'Bryan Tan',
                'vehicle_details' => 'Ford Ranger Next-Gen 2023 (CBT-1920)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'GCash / Maya',
                'payment_reference' => 'MY-20260904-49018',
                'tendered' => 3900.00,
                'notes' => 'Full double cab deep dish matting installation completed',
                'items' => [
                    ['code' => 'MAT-FD-RAN', 'qty' => 1],
                ],
                'user' => $admin,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 4, 15, 50, 10),
                'invoice_no' => 'INV-20260904-1004',
                'customer_search' => 'Kevin Lim',
                'fallback_name' => 'Kevin Lim',
                'vehicle_details' => 'Toyota Vios XLE 2021 (GAF-7731)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'Cash',
                'payment_reference' => null,
                'tendered' => 3500.00,
                'notes' => 'Vios 5D Diamond stitched matting installed front & rear',
                'items' => [
                    ['code' => 'MAT-TY-VIOS', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 4, 17, 22, 0),
                'invoice_no' => 'INV-20260904-1005',
                'customer_search' => null,
                'fallback_name' => 'Walk-in Customer 58',
                'vehicle_details' => null,
                'order_type' => 'Pick-up / Delivery',
                'installation_fee' => 0.00,
                'discount' => 0.00,
                'payment_method' => 'Bank Transfer',
                'payment_reference' => 'BDO-20260904-9821',
                'tendered' => 2950.00,
                'notes' => '4K Dual Dashcam kit pick-up order',
                'items' => [
                    ['code' => 'ACC-ELEC-DASH', 'qty' => 1],
                ],
                'user' => $admin,
            ],

            // === SEPTEMBER 6, 2026 (TODAY) ===
            [
                'datetime' => Carbon::create(2026, 9, 6, 9, 18, 20),
                'invoice_no' => 'INV-20260906-1001',
                'customer_search' => 'Dennis Ramos',
                'fallback_name' => 'Dennis Ramos',
                'vehicle_details' => 'Toyota Hilux Conquest 2022 (NBO-5512)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'GCash / Maya',
                'payment_reference' => 'GC-20260906-88341',
                'tendered' => 3800.00,
                'notes' => 'Sunday morning rush: Hilux Conquest TPE deep dish fitted',
                'items' => [
                    ['code' => 'MAT-TY-HLX', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 6, 11, 5, 45),
                'invoice_no' => 'INV-20260906-1002',
                'customer_search' => 'Ricardo Dizon',
                'fallback_name' => 'Ricardo Dizon',
                'vehicle_details' => 'Honda ADV 160 (773-ADV)',
                'order_type' => 'Walk-in',
                'installation_fee' => 0.00,
                'discount' => 0.00,
                'payment_method' => 'Cash',
                'payment_reference' => null,
                'tendered' => 1000.00,
                'notes' => 'ADV Stepboard matting pick-up',
                'items' => [
                    ['code' => 'MAT-MC-ADV', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 6, 13, 45, 12),
                'invoice_no' => 'INV-20260906-1003',
                'customer_search' => 'Carlo Gomez',
                'fallback_name' => 'Carlo Gomez',
                'vehicle_details' => 'Mitsubishi Montero Sport 2021 (NAN-3091)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'GCash / Maya',
                'payment_reference' => 'MY-20260906-12093',
                'tendered' => 4380.00,
                'notes' => 'Montero Sport 3-row matting & detailing care package',
                'items' => [
                    ['code' => 'MAT-MIT-MON', 'qty' => 1],
                    ['code' => 'ACC-CARE-MCR', 'qty' => 1],
                ],
                'user' => $admin,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 6, 15, 30, 50),
                'invoice_no' => 'INV-20260906-1004',
                'customer_search' => 'Mark Anthony Reyes',
                'fallback_name' => 'Mark Anthony Reyes',
                'vehicle_details' => 'Honda Civic RS 2022 (DAT-9812)',
                'order_type' => 'With Installation',
                'installation_fee' => 300.00,
                'discount' => 0.00,
                'payment_method' => 'Card',
                'payment_reference' => 'MC-7419',
                'tendered' => 3700.00,
                'notes' => 'Civic FE 5D Custom luxury floor matting installed',
                'items' => [
                    ['code' => 'MAT-HD-CIVIC', 'qty' => 1],
                ],
                'user' => $cashier,
            ],
            [
                'datetime' => Carbon::create(2026, 9, 6, 17, 15, 30),
                'invoice_no' => 'INV-20260906-1005',
                'customer_search' => null,
                'fallback_name' => 'Walk-in Customer 72',
                'vehicle_details' => 'Toyota Fortuner',
                'order_type' => 'Walk-in',
                'installation_fee' => 0.00,
                'discount' => 0.00,
                'payment_method' => 'Cash',
                'payment_reference' => null,
                'tendered' => 2000.00,
                'notes' => 'Late afternoon counter purchase: Trunk cargo tray & detailing spray kit',
                'items' => [
                    ['code' => 'ACC-TRK-FORT', 'qty' => 1],
                    ['code' => 'ACC-CARE-MCR', 'qty' => 1],
                ],
                'user' => $admin,
            ],
        ];

        foreach ($ordersDefinition as $ordData) {
            // Check if already seeded to prevent duplication
            if (Order::where('invoice_no', $ordData['invoice_no'])->exists()) {
                continue;
            }

            $customer = null;
            if (!empty($ordData['customer_search'])) {
                $customer = Customer::where('name', $ordData['customer_search'])->first();
            }

            $subtotal = 0;
            $itemsToCreate = [];

            foreach ($ordData['items'] as $it) {
                $prod = Product::where('product_code', $it['code'])->first();
                if ($prod) {
                    $lineTotal = $it['qty'] * $prod->unit_price;
                    $subtotal += $lineTotal;
                    $itemsToCreate[] = [
                        'product' => $prod,
                        'qty' => $it['qty'],
                        'unit_price' => $prod->unit_price,
                        'subtotal' => $lineTotal,
                    ];
                }
            }

            $totalAmount = max(0, $subtotal + $ordData['installation_fee'] - $ordData['discount']);
            $tendered = max($ordData['tendered'], $totalAmount);
            $change = max(0, $tendered - $totalAmount);

            $order = Order::create([
                'invoice_no' => $ordData['invoice_no'],
                'user_id' => $ordData['user']->id,
                'customer_id' => $customer ? $customer->id : null,
                'customer_name' => $customer ? $customer->name : $ordData['fallback_name'],
                'vehicle_details' => $customer ? "{$customer->vehicle_make_model} ({$customer->plate_number})" : $ordData['vehicle_details'],
                'order_type' => $ordData['order_type'],
                'subtotal' => $subtotal,
                'installation_fee' => $ordData['installation_fee'],
                'discount_amount' => $ordData['discount'],
                'total_amount' => $totalAmount,
                'payment_method' => $ordData['payment_method'],
                'payment_reference' => $ordData['payment_reference'],
                'amount_tendered' => $tendered,
                'change_amount' => $change,
                'payment_status' => 'Paid',
                'notes' => $ordData['notes'],
                'created_at' => $ordData['datetime'],
                'updated_at' => $ordData['datetime'],
            ]);

            foreach ($itemsToCreate as $itData) {
                $prod = $itData['product'];
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $prod->id,
                    'product_name' => $prod->name,
                    'quantity' => $itData['qty'],
                    'unit_price' => $itData['unit_price'],
                    'subtotal' => $itData['subtotal'],
                    'created_at' => $ordData['datetime'],
                    'updated_at' => $ordData['datetime'],
                ]);

                // Update inventory and log stock transaction
                $inv = Inventory::firstOrCreate(
                    ['product_id' => $prod->id],
                    ['quantity_on_hand' => 15, 'reorder_level' => 3]
                );
                $inv->quantity_on_hand = max(0, $inv->quantity_on_hand - $itData['qty']);
                $inv->save();

                StockTransaction::create([
                    'product_id' => $prod->id,
                    'user_id' => $ordData['user']->id,
                    'type' => 'pos_sale',
                    'quantity' => -$itData['qty'],
                    'balance_after' => $inv->quantity_on_hand,
                    'reference_no' => $order->invoice_no,
                    'remarks' => "POS Order: {$order->invoice_no}",
                    'created_at' => $ordData['datetime'],
                    'updated_at' => $ordData['datetime'],
                ]);
            }
        }
    }
}
