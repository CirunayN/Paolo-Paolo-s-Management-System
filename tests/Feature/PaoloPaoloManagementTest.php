<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Order;

class PaoloPaoloManagementTest extends TestCase
{
    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Paolo Paolo');
        $response->assertSee('D.A Matting & Accessories', false);
    }

    public function test_user_can_authenticate_and_access_dashboard(): void
    {
        $user = User::where('username', 'admin')->first();
        if (!$user) {
            $user = User::factory()->create(['role' => 'admin', 'username' => 'admin_test']);
        }

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Paolo Paolo');
        $response->assertSee('Today\'s Sales', false);
    }

    public function test_pos_terminal_loads_with_products(): void
    {
        $user = User::where('role', 'admin')->first();

        $response = $this->actingAs($user)->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('Launch POS Terminal');
        $response->assertSee('Toyota');
    }

    public function test_stock_in_increases_inventory_module(): void
    {
        $user = User::where('role', 'admin')->first();
        $product = Product::with('inventory')->first();
        $initialStock = (float)$product->inventory->quantity_on_hand;

        $receivedQty = 15;
        $response = $this->actingAs($user)->post('/stock-in', [
            'reference_no' => 'TEST-STK-' . time(),
            'received_date' => date('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_received' => $receivedQty,
                    'cost_per_unit' => $product->cost_price,
                ]
            ]
        ]);

        $response->assertRedirect(route('stock-in.index'));

        // Refresh and verify inventory updated
        $product->inventory->refresh();
        $expectedStock = $initialStock + $receivedQty;
        $this->assertEquals($expectedStock, (float)$product->inventory->quantity_on_hand);
    }

    public function test_pos_checkout_creates_order_and_deducts_inventory(): void
    {
        $user = User::where('role', 'admin')->first();
        $product = Product::with('inventory')->where('unit_price', '>', 0)->first();
        $initialStock = (float)$product->inventory->quantity_on_hand;

        $saleQty = 2;
        $response = $this->actingAs($user)->postJson('/pos/checkout', [
            'cart' => [
                [
                    'id' => $product->id,
                    'quantity' => $saleQty,
                    'price' => $product->unit_price,
                ]
            ],
            'order_type' => 'Walk-in',
            'payment_method' => 'Cash',
            'amount_tendered' => $product->unit_price * $saleQty,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify inventory deducted
        $product->inventory->refresh();
        $this->assertEquals($initialStock - $saleQty, (float)$product->inventory->quantity_on_hand);
    }
}
