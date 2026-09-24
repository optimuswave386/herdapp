<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->admin = User::factory()->create(['name' => 'Admin']);
    $this->admin->forceFill(['is_admin' => true])->save();
    $this->customer = User::factory()->create(['name' => 'Casey']);
    $this->lamp = Product::create(['name' => 'Lamp', 'description' => 'Bright', 'price' => 25.50, 'stock_quantity' => 10, 'category' => 'Home', 'status' => 'active']);
    $this->desk = Product::create(['name' => 'Desk', 'description' => 'Sturdy', 'price' => 100, 'stock_quantity' => 3, 'category' => 'Home', 'status' => 'active']);
});

function shippingDetails(array $override = []): array
{
    return array_merge([
        'shipping_name' => 'Casey Customer', 'shipping_address' => '1 Main St', 'shipping_city' => 'Little Rock',
        'shipping_postal_code' => '72201', 'shipping_country' => 'United States', 'payment_method' => 'pay_on_delivery',
    ], $override);
}

function cartJson(array $lines): string
{
    // Same shape the browser keeps in localStorage: keyed by product id.
    $cart = [];
    foreach ($lines as $id => $qty) {
        $cart[$id] = ['product_id' => $id, 'quantity' => $qty, 'product_name' => 'Tampered', 'price' => 0.01];
    }

    return json_encode($cart);
}

// ---------------------------------------------------------------- product management

it('lets an admin create a product with an image', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)->post('/admin/products/create', [
        'name' => 'Vase', 'description' => 'Blue', 'price' => '19.99', 'stock_quantity' => 7,
        'category' => 'Decor', 'status' => 'active', 'image' => UploadedFile::fake()->image('vase.jpg'),
    ])->assertRedirect(route('admin.products.index'))->assertSessionHas('success');

    $vase = Product::where('name', 'Vase')->firstOrFail();
    expect((float) $vase->price)->toBe(19.99)
        ->and($vase->stock_quantity)->toBe(7)
        ->and($vase->image_url)->toStartWith('/storage/products/');
    Storage::disk('public')->assertExists($vase->storedImagePath());
});

it('validates product input', function () {
    $this->actingAs($this->admin)->post('/admin/products/create', [
        'name' => '', 'price' => '-5', 'stock_quantity' => 'lots', 'status' => 'weird',
    ])->assertSessionHasErrors(['name', 'price', 'stock_quantity', 'status']);

    expect(Product::count())->toBe(2);
});

it('lets an admin edit a product and swap or remove its image', function () {
    Storage::fake('public');
    $this->actingAs($this->admin)->get("/admin/products/edit/{$this->lamp->id}")->assertOk()->assertSee('Save changes');

    $payload = ['name' => 'Lamp XL', 'description' => 'Brighter', 'price' => '30', 'stock_quantity' => 4, 'category' => 'Home', 'status' => 'inactive'];
    $this->actingAs($this->admin)->patch("/admin/products/edit/{$this->lamp->id}", $payload + ['image' => UploadedFile::fake()->image('a.png')])
        ->assertRedirect(route('admin.products.index'));

    $first = $this->lamp->fresh();
    expect($first->name)->toBe('Lamp XL')->and($first->status)->toBe('inactive');
    Storage::disk('public')->assertExists($first->storedImagePath());

    $this->actingAs($this->admin)->patch("/admin/products/edit/{$this->lamp->id}", $payload + ['remove_image' => 1]);
    expect($this->lamp->fresh()->image_url)->toBeNull();
    Storage::disk('public')->assertMissing($first->storedImagePath());
});

it('deletes a product but keeps past orders readable', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id]);
    $order->items()->create(['product_id' => $this->lamp->id, 'product_name' => 'Lamp', 'unit_price' => 25.50, 'quantity' => 1, 'line_total' => 25.50]);

    $this->actingAs($this->admin)->delete("/admin/products/delete/{$this->lamp->id}")->assertRedirect(route('admin.products.index'));

    expect(Product::find($this->lamp->id))->toBeNull();
    $item = OrderItem::first();
    expect($item->product_id)->toBeNull()->and($item->product_name)->toBe('Lamp');
    $this->actingAs($this->admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('product deleted');
});

it('keeps product management closed to customers and guests', function () {
    $writes = [
        ['post', '/admin/products/create', ['name' => 'X', 'price' => 1, 'stock_quantity' => 1, 'status' => 'active']],
        ['patch', "/admin/products/edit/{$this->lamp->id}", ['name' => 'Hacked', 'price' => 1, 'stock_quantity' => 1, 'status' => 'active']],
        ['delete', "/admin/products/delete/{$this->lamp->id}", []],
    ];
    foreach ($writes as [$method, $url, $data]) {
        $this->actingAs($this->customer)->{$method}($url, $data)->assertForbidden();
        auth()->logout();
        $this->{$method}($url, $data)->assertRedirect('/login');
    }
    foreach (['/admin/products/create', "/admin/products/edit/{$this->lamp->id}"] as $url) {
        $this->actingAs($this->customer)->get($url)->assertForbidden();
    }

    expect($this->lamp->fresh()->name)->toBe('Lamp')->and(Product::count())->toBe(2);
});

it('shows only active products in the shop and hides inactive detail pages', function () {
    $hidden = Product::create(['name' => 'Secret', 'price' => 5, 'stock_quantity' => 1, 'status' => 'inactive']);

    $this->actingAs($this->customer)->get('/products')->assertOk()->assertSee('Lamp')->assertDontSee('Secret');
    $this->actingAs($this->customer)->get("/products/view/{$this->lamp->id}")->assertOk()->assertSee('Lamp')->assertSee('$25.50');
    $this->actingAs($this->customer)->get("/products/view/{$hidden->id}")->assertNotFound();
    $this->actingAs($this->admin)->get("/products/view/{$hidden->id}")->assertOk()->assertSee('Secret');
});

// ---------------------------------------------------------------- checkout

it('places an order priced from the database, not the browser cart', function () {
    $response = $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails([
        'cart' => cartJson([$this->lamp->id => 2, $this->desk->id => 1]),
    ]));

    $order = Order::with('items')->firstOrFail();
    $response->assertRedirect(route('orders.show', $order))->assertSessionHas('order_placed');

    // 2 x 25.50 + 1 x 100.00, regardless of the 0.01 the tampered cart claimed.
    expect((float) $order->total)->toBe(151.0)
        ->and($order->status)->toBe('pending')
        ->and($order->user_id)->toBe($this->customer->id)
        ->and($order->number)->toBe('ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT))
        ->and($order->items)->toHaveCount(2)
        ->and($order->items->firstWhere('product_name', 'Lamp')->quantity)->toBe(2)
        ->and((float) $order->items->firstWhere('product_name', 'Lamp')->unit_price)->toBe(25.5);

    expect($this->lamp->fresh()->stock_quantity)->toBe(8)->and($this->desk->fresh()->stock_quantity)->toBe(2);
});

it('refuses to oversell and leaves stock and orders untouched', function () {
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails([
        'cart' => cartJson([$this->lamp->id => 1, $this->desk->id => 5]), // only 3 desks
    ]))->assertSessionHasErrors('cart');

    expect(Order::count())->toBe(0)
        ->and($this->lamp->fresh()->stock_quantity)->toBe(10)
        ->and($this->desk->fresh()->stock_quantity)->toBe(3);
});

it('rejects inactive products, empty carts and bad details', function () {
    $this->lamp->update(['status' => 'inactive']);
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails(['cart' => cartJson([$this->lamp->id => 1])]))->assertSessionHasErrors('cart');
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails(['cart' => '{}']))->assertSessionHasErrors('items');
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails(['cart' => 'not json']))->assertSessionHasErrors('items');
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', ['cart' => cartJson([$this->desk->id => 1])])
        ->assertSessionHasErrors(['shipping_name', 'shipping_address', 'payment_method']);
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails(['cart' => cartJson([$this->desk->id => 500])]))->assertSessionHasErrors('items.0.quantity');

    expect(Order::count())->toBe(0);
});

it('requires a login to check out', function () {
    $this->post('/shoppingcart/checkout', shippingDetails(['cart' => cartJson([$this->desk->id => 1])]))->assertRedirect('/login');
    $this->get('/shoppingcart/checkout')->assertRedirect('/login');
});

it('renders the checkout page with the order form', function () {
    $this->actingAs($this->customer)->get('/shoppingcart/checkout')->assertOk()
        ->assertSee('Shipping details')->assertSee('Place order')->assertSee('name="cart"', false);
});

// ---------------------------------------------------------------- viewing orders

it('shows an order only to its owner and to admins', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id]);
    $stranger = User::factory()->create();

    $this->actingAs($this->customer)->get("/orders/{$order->id}")->assertOk()->assertSee($order->number);
    $this->actingAs($this->admin)->get("/orders/{$order->id}")->assertOk();
    $this->actingAs($stranger)->get("/orders/{$order->id}")->assertForbidden();

    $this->actingAs($this->customer)->get('/orders')->assertOk()->assertSee($order->number);
    $this->actingAs($stranger)->get('/orders')->assertOk()->assertDontSee($order->number);
});

// ---------------------------------------------------------------- admin order management

it('lets an admin move an order through statuses and cancelling returns stock', function () {
    $this->actingAs($this->customer)->post('/shoppingcart/checkout', shippingDetails(['cart' => cartJson([$this->desk->id => 2])]));
    $order = Order::firstOrFail();
    expect($this->desk->fresh()->stock_quantity)->toBe(1);
    auth()->logout();

    $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), ['status' => 'paid'])->assertSessionHas('success');
    expect($order->fresh()->status)->toBe('paid');

    $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), ['status' => 'cancelled']);
    expect($order->fresh()->status)->toBe('cancelled')->and($this->desk->fresh()->stock_quantity)->toBe(3);

    // final: cannot reopen, and stock must not be taken again
    $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), ['status' => 'paid'])->assertSessionHas('error');
    expect($order->fresh()->status)->toBe('cancelled')->and($this->desk->fresh()->stock_quantity)->toBe(3);

    $this->actingAs($this->admin)->patch(route('admin.orders.update', $order), ['status' => 'bogus'])->assertSessionHasErrors('status');
});

it('keeps order management closed to customers', function () {
    $order = Order::factory()->create(['user_id' => $this->customer->id, 'status' => 'pending']);

    $this->actingAs($this->customer)->get('/admin/orders')->assertForbidden();
    $this->actingAs($this->customer)->get("/admin/orders/{$order->id}")->assertForbidden();
    $this->actingAs($this->customer)->patch("/admin/orders/{$order->id}", ['status' => 'paid'])->assertForbidden();
    expect($order->fresh()->status)->toBe('pending');
});

it('lists and filters orders for admins', function () {
    $paid = Order::factory()->status('paid')->create();
    $pending = Order::factory()->status('pending')->create();

    $this->actingAs($this->admin)->get('/admin/orders')->assertOk()->assertSee($paid->number)->assertSee($pending->number);
    $this->actingAs($this->admin)->get('/admin/orders?status=pending')->assertOk()->assertSee($pending->number)->assertDontSee($paid->number);
});

// ---------------------------------------------------------------- dashboard revenue

it('shows correct revenue, order counts and charts on the dashboard', function () {
    Order::factory()->status('paid')->create(['total' => 100]);
    Order::factory()->status('shipped')->create(['total' => 50]);
    Order::factory()->status('pending')->create(['total' => 999]);
    Order::factory()->status('cancelled')->create(['total' => 500]);
    Order::factory()->status('paid')->create(['total' => 200, 'created_at' => now()->subDays(40)]);

    $recentPaid = Order::where('status', 'paid')->where('created_at', '>=', now()->subDays(30))->first();
    $recentPaid->items()->create(['product_id' => $this->lamp->id, 'product_name' => 'Lamp', 'unit_price' => 100, 'quantity' => 1, 'line_total' => 100]);

    $html = $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->getContent();

    expect($html)->toContain('$150.00')                 // 100 paid + 50 shipped; not pending, cancelled or the 40-day-old order
        ->and($html)->toContain('$75.00')               // average of the two revenue orders (150 / 2)
        ->and($html)->toContain('1 pending order')      // orders needing action
        // the revenue card itself: exactly $150.00, so the pending $999 and cancelled $500 are excluded
        ->and($html)->toMatch('/Revenue · 30 days<\/p>\s*<p[^>]*>\$150\.00</')
        ->and(substr_count($html, 'data-chart='))->toBe(6);

    // monthly chart includes the old paid order in its own month, but the 30-day KPI does not
    preg_match_all("/data-chart='([^']+)'/", $html, $m);
    $revenueChart = collect($m[1])->map(fn ($j) => json_decode(html_entity_decode($j), true))->firstWhere('label', 'Revenue');
    expect(array_sum($revenueChart['data']))->toBeGreaterThanOrEqual(150.0);
});

it('handles a store with no orders yet', function () {
    $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->assertSee('$0.00')->assertSee('No orders yet.');
});
