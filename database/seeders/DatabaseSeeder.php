<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockTransaction;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BackupSetting;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with physical automotive products, 30 days of sales, and backup settings.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@paolopaolo.com'],
            [
                'name' => 'Paolo Admin',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '0917-123-4567',
                'is_active' => true,
            ]
        );

        $cashier = User::firstOrCreate(
            ['email' => 'cashier@paolopaolo.com'],
            [
                'name' => 'Maria Cashier',
                'username' => 'cashier',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'phone' => '0928-765-4321',
                'is_active' => true,
            ]
        );

        // 2. Categories (Physical Automotive Products Only)
        $categoriesData = [
            ['name' => 'Deep Dish Matting', 'slug' => 'deep-dish-matting', 'icon' => 'fas fa-shield-alt', 'description' => 'Precision-fit, high-walled TPE/rubber deep dish car mats.'],
            ['name' => '5D / Diamond Matting', 'slug' => '5d-diamond-matting', 'icon' => 'fas fa-gem', 'description' => 'Luxury diamond stitched leatherette vehicle flooring.'],
            ['name' => 'Coil & Rubber Mats', 'slug' => 'coil-rubber-mats', 'icon' => 'fas fa-scroll', 'description' => 'Heavy-duty noodle coil rolls, cut-to-measure & universal mats.'],
            ['name' => 'Motorcycle Matting', 'slug' => 'motorcycle-matting', 'icon' => 'fas fa-motorcycle', 'description' => 'Scooter and motorcycle footboard mats and accessories.'],
            ['name' => 'Trunk & Cargo Trays', 'slug' => 'trunk-cargo-trays', 'icon' => 'fas fa-box-open', 'description' => 'Waterproof custom trunk liners and cargo floor trays.'],
            ['name' => 'Interior Accessories', 'slug' => 'interior-accessories', 'icon' => 'fas fa-car-side', 'description' => 'Seat covers, organizers, dashcams, steering wheel wraps.'],
            ['name' => 'Exterior Accessories', 'slug' => 'exterior-accessories', 'icon' => 'fas fa-sun', 'description' => 'Rain visors, scuff plates, mud guards, and step boards.'],
            ['name' => 'Car Care & Detailing', 'slug' => 'car-care-detailing', 'icon' => 'fas fa-spray-can', 'description' => 'Mat cleaners, microfiber cloths, interior protectants.'],
        ];

        $categories = [];
        foreach ($categoriesData as $cData) {
            $cat = Category::firstOrCreate(['slug' => $cData['slug']], $cData);
            $categories[$cData['slug']] = $cat->id;
        }

        // 3. Suppliers
        $suppliers = [
            Supplier::firstOrCreate(
                ['supplier_name' => 'Direct Auto Matting Imports Corp.'],
                [
                    'contact_person' => 'Carlos Tan',
                    'contact_number' => '0917-555-0101',
                    'email' => 'carlos@directautomatting.ph',
                    'address' => 'Valenzuela Industrial Park, Metro Manila',
                    'notes' => 'Primary manufacturer & importer of TPE Deep Dish & Diamond mats.',
                ]
            ),
            Supplier::firstOrCreate(
                ['supplier_name' => 'ProCar Accessories Distribution'],
                [
                    'contact_person' => 'Jenny Lim',
                    'contact_number' => '0922-888-2345',
                    'email' => 'orders@procardistrib.ph',
                    'address' => 'Banawe St, Quezon City',
                    'notes' => 'Supplier for dashcams, LED accessories, trunk trays, visors.',
                ]
            ),
            Supplier::firstOrCreate(
                ['supplier_name' => 'MotoPro Matting & Parts Wholesaler'],
                [
                    'contact_person' => 'Ricky Santos',
                    'contact_number' => '0918-999-7766',
                    'email' => 'ricky@motopromats.ph',
                    'address' => '10th Ave, Caloocan City',
                    'notes' => 'Specializes in NMAX, Aerox, ADV, Click motorcycle floorboards.',
                ]
            ),
        ];

        // 4. Customers with registered vehicles (Davao City Area)
        $customersData = [
            ['name' => 'Juan Dela Cruz', 'contact_number' => '0917-889-1122', 'email' => 'juan.delacruz@gmail.com', 'vehicle_make_model' => 'Toyota Fortuner 2023', 'plate_number' => 'NBH-4821', 'address' => 'Matina, Davao City'],
            ['name' => 'Mark Anthony Reyes', 'contact_number' => '0920-554-3321', 'email' => 'mark.reyes@yahoo.com', 'vehicle_make_model' => 'Honda Civic RS 2022', 'plate_number' => 'DAT-9812', 'address' => 'Buhangin, Davao City'],
            ['name' => 'Carlo Gomez', 'contact_number' => '0918-333-4455', 'email' => 'carlo.gomez@gmail.com', 'vehicle_make_model' => 'Mitsubishi Montero Sport 2021', 'plate_number' => 'NAN-3091', 'address' => 'Bajada, Davao City'],
            ['name' => 'Bryan Tan', 'contact_number' => '0922-777-6611', 'email' => 'bryan.tan@outlook.com', 'vehicle_make_model' => 'Ford Ranger Next-Gen 2023', 'plate_number' => 'CBT-1920', 'address' => 'Lanang, Davao City'],
            ['name' => 'Dennis Ramos', 'contact_number' => '0915-442-8899', 'email' => 'dennis.ramos@gmail.com', 'vehicle_make_model' => 'Toyota Hilux Conquest 2022', 'plate_number' => 'NBO-5512', 'address' => 'Toril, Davao City'],
            ['name' => 'Kevin Lim', 'contact_number' => '0927-111-9922', 'email' => 'kevin.lim@gmail.com', 'vehicle_make_model' => 'Toyota Vios XLE 2021', 'plate_number' => 'GAF-7731', 'address' => 'Ecoland, Davao City'],
            ['name' => 'Alex Cruz', 'contact_number' => '0919-888-2233', 'email' => 'alex.cruz@yahoo.com', 'vehicle_make_model' => 'Yamaha NMAX 155 V2', 'plate_number' => '481-NMX', 'address' => 'Poblacion District, Davao City'],
            ['name' => 'Ricardo Dizon', 'contact_number' => '0921-665-4411', 'email' => 'ricardo.dizon@gmail.com', 'vehicle_make_model' => 'Honda ADV 160', 'plate_number' => '773-ADV', 'address' => 'Bangkal, Davao City'],
        ];

        $customers = [];
        foreach ($customersData as $c) {
            $customers[] = Customer::firstOrCreate(['contact_number' => $c['contact_number']], $c);
        }

        // 5. Physical Products Catalog ONLY (with multi-image arrays)
        $productsData = [
            [
                'product_code' => 'MAT-TY-FORT',
                'name' => 'Toyota Fortuner 3-Row TPE Deep Dish Matting (2016-2024)',
                'category_id' => $categories['deep-dish-matting'],
                'vehicle_brand' => 'Toyota',
                'vehicle_model' => 'Fortuner 2016-2024',
                'material_type' => 'High-Grade TPE Deep Dish',
                'unit_of_measure' => 'Set',
                'cost_price' => 2400.00,
                'unit_price' => 3800.00,
                'stock_alert_level' => 4,
                'image_path' => 'images/products/mat-deepdish-suv.svg',
                'images' => ['images/products/mat-deepdish-suv.svg', 'images/products/mat-diamond-sedan.svg', 'images/products/acc-trunk-tray.svg'],
                'description' => 'Precision laser-scanned 3D/5D deep dish tray mats. Waterproof, anti-slip, odorless TPE material with 3-row coverage.',
                'stock' => 18,
            ],
            [
                'product_code' => 'MAT-TY-HLX',
                'name' => 'Toyota Hilux Double Cab Deep Dish Matting (2016-2024)',
                'category_id' => $categories['deep-dish-matting'],
                'vehicle_brand' => 'Toyota',
                'vehicle_model' => 'Hilux 2016-2024',
                'material_type' => 'Heavy Duty TPE',
                'unit_of_measure' => 'Set',
                'cost_price' => 2200.00,
                'unit_price' => 3500.00,
                'stock_alert_level' => 3,
                'image_path' => 'images/products/mat-deepdish-pickup.svg',
                'images' => ['images/products/mat-deepdish-pickup.svg', 'images/products/mat-deepdish-suv.svg'],
                'description' => 'Rugged deep wall tray mats for Hilux front and rear cabin. Mud and spill proof.',
                'stock' => 14,
            ],
            [
                'product_code' => 'MAT-TY-VIOS',
                'name' => 'Toyota Vios 5D Diamond Stitched Matting (2018-2024)',
                'category_id' => $categories['5d-diamond-matting'],
                'vehicle_brand' => 'Toyota',
                'vehicle_model' => 'Vios 2018-2024',
                'material_type' => '5D Diamond Leatherette + Coil Top',
                'unit_of_measure' => 'Set',
                'cost_price' => 1750.00,
                'unit_price' => 2800.00,
                'stock_alert_level' => 4,
                'image_path' => 'images/products/mat-diamond-sedan.svg',
                'images' => ['images/products/mat-diamond-sedan.svg', 'images/products/mat-coil-roll.svg'],
                'description' => 'Luxury stitched black leatherette with red border and detachable coil layer for easy cleaning.',
                'stock' => 12,
            ],
            [
                'product_code' => 'MAT-MB-MONT',
                'name' => 'Mitsubishi Montero Sport Deep Dish Matting 3-Rows (2016-2024)',
                'category_id' => $categories['deep-dish-matting'],
                'vehicle_brand' => 'Mitsubishi',
                'vehicle_model' => 'Montero Sport 2016-2024',
                'material_type' => 'OEM Quality TPE',
                'unit_of_measure' => 'Set',
                'cost_price' => 2400.00,
                'unit_price' => 3800.00,
                'stock_alert_level' => 4,
                'image_path' => 'images/products/mat-deepdish-suv.svg',
                'images' => ['images/products/mat-deepdish-suv.svg', 'images/products/acc-trunk-tray.svg'],
                'description' => 'Complete front, 2nd row, and 3rd row deep dish floor protection designed for Montero Sport.',
                'stock' => 15,
            ],
            [
                'product_code' => 'MAT-FD-RNGR',
                'name' => 'Ford Ranger Next-Gen / Everest Deep Dish Matting (2022-2024)',
                'category_id' => $categories['deep-dish-matting'],
                'vehicle_brand' => 'Ford',
                'vehicle_model' => 'Ranger / Everest 2022-2024',
                'material_type' => 'Rugged TPE',
                'unit_of_measure' => 'Set',
                'cost_price' => 2500.00,
                'unit_price' => 3950.00,
                'stock_alert_level' => 3,
                'image_path' => 'images/products/mat-deepdish-pickup.svg',
                'images' => ['images/products/mat-deepdish-pickup.svg', 'images/products/mat-diamond-sedan.svg'],
                'description' => 'Specially molded for Next-Gen Ford Ranger cabin. Extreme durability and retention hooks.',
                'stock' => 9,
            ],
            [
                'product_code' => 'MAT-HD-CIV',
                'name' => 'Honda Civic FE 5D Custom Floor Matting (2022-2024)',
                'category_id' => $categories['5d-diamond-matting'],
                'vehicle_brand' => 'Honda',
                'vehicle_model' => 'Civic FE 2022-2024',
                'material_type' => '5D Premium Carbon Texture',
                'unit_of_measure' => 'Set',
                'cost_price' => 2100.00,
                'unit_price' => 3300.00,
                'stock_alert_level' => 3,
                'image_path' => 'images/products/mat-diamond-sedan.svg',
                'images' => ['images/products/mat-diamond-sedan.svg'],
                'description' => 'Sporty carbon-look finish custom tailored for the 11th Gen Honda Civic.',
                'stock' => 7,
            ],
            [
                'product_code' => 'MAT-COIL-ROLL',
                'name' => 'Universal Heavy Duty Coil / Noodle Mat Roll (1.2m x 9m)',
                'category_id' => $categories['coil-rubber-mats'],
                'vehicle_brand' => 'Universal',
                'vehicle_model' => 'Universal',
                'material_type' => 'High-Density PVC Coil',
                'unit_of_measure' => 'Roll',
                'cost_price' => 2900.00,
                'unit_price' => 4500.00,
                'stock_alert_level' => 2,
                'image_path' => 'images/products/mat-coil-roll.svg',
                'images' => ['images/products/mat-coil-roll.svg'],
                'description' => 'Full 9-meter roll for custom cutting car flooring, commercial mats, and vans. Traps dirt and sand.',
                'stock' => 6,
            ],
            [
                'product_code' => 'MAT-MC-NMAX',
                'name' => 'Yamaha NMAX V2 / V3 Rubber Footboard Matting',
                'category_id' => $categories['motorcycle-matting'],
                'vehicle_brand' => 'Yamaha',
                'vehicle_model' => 'NMAX 155 V2 / V3',
                'material_type' => 'Anti-Slip Vulcanized Rubber',
                'unit_of_measure' => 'Pc',
                'cost_price' => 350.00,
                'unit_price' => 650.00,
                'stock_alert_level' => 5,
                'image_path' => 'images/products/mat-motorcycle-step.svg',
                'images' => ['images/products/mat-motorcycle-step.svg'],
                'description' => 'Durable embossed footboard floor mat with stainless accents and mounting hardware.',
                'stock' => 22,
            ],
            [
                'product_code' => 'MAT-MC-ADV',
                'name' => 'Honda ADV 150 / 160 Stepboard Matting',
                'category_id' => $categories['motorcycle-matting'],
                'vehicle_brand' => 'Honda',
                'vehicle_model' => 'ADV 150 / 160',
                'material_type' => 'Thick Grip Rubber',
                'unit_of_measure' => 'Pc',
                'cost_price' => 320.00,
                'unit_price' => 580.00,
                'stock_alert_level' => 5,
                'image_path' => 'images/products/mat-motorcycle-step.svg',
                'images' => ['images/products/mat-motorcycle-step.svg'],
                'description' => 'Adventure styling high-grip stepboard mat for Honda ADV.',
                'stock' => 19,
            ],
            [
                'product_code' => 'ACC-TRK-FORT',
                'name' => 'Fortuner OEM Style Trunk Cargo Tray Liner',
                'category_id' => $categories['trunk-cargo-trays'],
                'vehicle_brand' => 'Toyota',
                'vehicle_model' => 'Fortuner 2016-2024',
                'material_type' => 'HDPE Flexible Plastic',
                'unit_of_measure' => 'Pc',
                'cost_price' => 850.00,
                'unit_price' => 1450.00,
                'stock_alert_level' => 3,
                'image_path' => 'images/products/acc-trunk-tray.svg',
                'images' => ['images/products/acc-trunk-tray.svg'],
                'description' => 'Spill-proof rear cargo tray. Keeps trunk floor clean from grocery liquids, tools, and dirt.',
                'stock' => 11,
            ],
            [
                'product_code' => 'ACC-ELEC-DASH',
                'name' => 'Dual Lens 4K Ultra HD Car Dashcam (Front + Rear)',
                'category_id' => $categories['interior-accessories'],
                'vehicle_brand' => 'Universal',
                'vehicle_model' => 'Universal',
                'material_type' => 'Electronics / Optical Glass',
                'unit_of_measure' => 'Set',
                'cost_price' => 1800.00,
                'unit_price' => 2950.00,
                'stock_alert_level' => 2,
                'image_path' => 'images/products/acc-dashcam.svg',
                'images' => ['images/products/acc-dashcam.svg'],
                'description' => 'WiFi enabled, Sony night vision sensor, G-sensor parking monitor, rear backup camera included.',
                'stock' => 8,
            ],
            [
                'product_code' => 'ACC-CARE-MCR',
                'name' => 'Paolo Paolo Pro Microfiber & Mat Detailing Kit (5-in-1)',
                'category_id' => $categories['car-care-detailing'],
                'vehicle_brand' => 'Universal',
                'vehicle_model' => 'Universal',
                'material_type' => 'Microfiber / Cleaner',
                'unit_of_measure' => 'Set',
                'cost_price' => 190.00,
                'unit_price' => 380.00,
                'stock_alert_level' => 6,
                'image_path' => 'images/products/acc-care-kit.svg',
                'images' => ['images/products/acc-care-kit.svg'],
                'description' => 'Includes 500ml rubber & TPE conditioning spray, soft bristle mat brush, and 3 edgeless microfiber cloths.',
                'stock' => 35,
            ],
        ];

        $productModels = [];
        foreach ($productsData as $pData) {
            $stock = $pData['stock'];
            unset($pData['stock']);

            $product = Product::updateOrCreate(['product_code' => $pData['product_code']], $pData);
            $productModels[] = $product;

            Inventory::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity_on_hand' => $stock,
                    'reorder_level' => $product->stock_alert_level,
                    'last_restocked_at' => now()->subDays(3),
                ]
            );

            StockTransaction::firstOrCreate(
                ['product_id' => $product->id, 'type' => 'stock_in', 'reference_no' => 'INIT-STOCK-30D'],
                [
                    'user_id' => $admin->id,
                    'quantity' => $stock,
                    'balance_after' => $stock,
                    'remarks' => 'Baseline physical inventory',
                    'created_at' => now()->subDays(30),
                    'updated_at' => now()->subDays(30),
                ]
            );
        }

        // 6. Stock-In Deliveries from suppliers
        $shipments = [
            ['days_ago' => 28, 'supplier' => $suppliers[0], 'ref' => 'STK-2026-0806', 'notes' => 'Container shipment: Full pallet of TPE Fortuner & Hilux sets'],
            ['days_ago' => 21, 'supplier' => $suppliers[1], 'ref' => 'STK-2026-0813', 'notes' => 'Banawe shipment: 4K Dashcams & Fortuner Cargo Trays'],
            ['days_ago' => 14, 'supplier' => $suppliers[2], 'ref' => 'STK-2026-0820', 'notes' => 'Caloocan shipment: NMAX & ADV Motorcycle footboards'],
            ['days_ago' => 7,  'supplier' => $suppliers[0], 'ref' => 'STK-2026-0827', 'notes' => 'Restock: Montero & Ranger 5D Diamond mats'],
            ['days_ago' => 2,  'supplier' => $suppliers[1], 'ref' => 'STK-2026-0901', 'notes' => 'Monthly detailing kits and microfiber cloths'],
        ];

        foreach ($shipments as $s) {
            $sDate = now()->subDays($s['days_ago']);
            $stockIn = StockIn::firstOrCreate(
                ['reference_no' => $s['ref']],
                [
                    'supplier_id' => $s['supplier']->id,
                    'user_id' => $admin->id,
                    'total_cost' => 0,
                    'notes' => $s['notes'],
                    'received_date' => $sDate->format('Y-m-d'),
                    'created_at' => $sDate,
                    'updated_at' => $sDate,
                ]
            );

            $shipmentTotal = 0;
            $itemsToRestock = array_slice($productModels, rand(0, 3), rand(2, 4));
            foreach ($itemsToRestock as $item) {
                $qtyRec = rand(8, 20);
                $sub = $qtyRec * $item->cost_price;
                $shipmentTotal += $sub;

                StockInItem::firstOrCreate(
                    ['stock_in_id' => $stockIn->id, 'product_id' => $item->id],
                    [
                        'quantity_received' => $qtyRec,
                        'cost_per_unit' => $item->cost_price,
                        'subtotal' => $sub,
                        'created_at' => $sDate,
                        'updated_at' => $sDate,
                    ]
                );
            }

            $stockIn->total_cost = $shipmentTotal;
            $stockIn->save();
        }

        // 7. Orders & Sales Transactions across 30 Days
        $paymentMethodPool = ['Cash', 'Cash', 'Cash', 'GCash / Maya', 'GCash / Maya', 'Card', 'Bank Transfer'];
        $orderTypePool = ['Walk-in', 'Walk-in', 'With Installation', 'With Installation', 'Pick-up / Delivery'];
        $orderSeq = 1001;

        for ($daysAgo = 30; $daysAgo >= 0; $daysAgo--) {
            $currentDate = now()->subDays($daysAgo);
            $ordersCountForDay = rand(3, 6);

            for ($k = 1; $k <= $ordersCountForDay; $k++) {
                $hour = rand(9, 18);
                $minute = rand(0, 59);
                $orderTimestamp = (clone $currentDate)->setHour($hour)->setMinute($minute)->setSecond(rand(0, 59));

                $isCustomer = (rand(1, 10) > 4);
                $cust = $isCustomer ? $customers[array_rand($customers)] : null;
                $custName = $cust ? $cust->name : ('Walk-in Customer ' . rand(10, 99));
                $vehDetails = $cust ? "{$cust->vehicle_make_model} ({$cust->plate_number})" : null;

                $orderType = $orderTypePool[array_rand($orderTypePool)];
                $paymentMethod = $paymentMethodPool[array_rand($paymentMethodPool)];
                $installationFee = ($orderType === 'With Installation') ? (rand(1, 2) * 300) : 0;
                $cashierUser = (rand(1, 10) > 3) ? $cashier : $admin;

                $itemCount = rand(1, 2);
                $selectedProducts = [];
                for ($i = 0; $i < $itemCount; $i++) {
                    $selectedProducts[] = $productModels[array_rand($productModels)];
                }

                $subtotal = 0;
                $orderItemsData = [];
                foreach ($selectedProducts as $prod) {
                    $qty = 1;
                    $lineSub = $qty * $prod->unit_price;
                    $subtotal += $lineSub;
                    $orderItemsData[] = [
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'quantity' => $qty,
                        'unit_price' => $prod->unit_price,
                        'subtotal' => $lineSub,
                    ];
                }

                $discount = (rand(1, 10) === 10) ? 100.00 : 0.00;
                $totalAmount = max(0, $subtotal + $installationFee - $discount);

                $tendered = $totalAmount;
                $change = 0;
                if ($paymentMethod === 'Cash') {
                    $tenderPresets = [$totalAmount, ceil($totalAmount / 500) * 500, ceil($totalAmount / 1000) * 1000];
                    $tendered = $tenderPresets[array_rand($tenderPresets)];
                    $change = max(0, $tendered - $totalAmount);
                }

                $dateStr = $orderTimestamp->format('Ymd');
                $invNo = "INV-{$dateStr}-{$orderSeq}";
                $orderSeq++;

                $order = Order::create([
                    'invoice_no' => $invNo,
                    'user_id' => $cashierUser->id,
                    'customer_id' => $cust ? $cust->id : null,
                    'customer_name' => $custName,
                    'vehicle_details' => $vehDetails,
                    'order_type' => $orderType,
                    'subtotal' => $subtotal,
                    'installation_fee' => $installationFee,
                    'discount_amount' => $discount,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'amount_tendered' => $tendered,
                    'change_amount' => $change,
                    'payment_status' => 'Paid',
                    'notes' => ($orderType === 'With Installation') ? 'Installed with floor retention clips' : null,
                    'created_at' => $orderTimestamp,
                    'updated_at' => $orderTimestamp,
                ]);

                foreach ($orderItemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product_id'],
                        'product_name' => $itemData['product_name'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $itemData['subtotal'],
                        'created_at' => $orderTimestamp,
                        'updated_at' => $orderTimestamp,
                    ]);
                }
            }
        }

        // 8. Initialize Backup Settings
        BackupSetting::getSettings();

        // 9. Additional seed for Sep 4 and Sep 6
        $this->call(Sep4AndSep6Seeder::class);
    }
}
