<?php

use App\Models\Product;
use App\Models\User;

/**
 * Smoke tests: every major page renders without a server error for an admin,
 * the admin area is closed to everyone else, and the dashboard shows real data.
 *
 * Run:   php artisan test --filter=PagesSmokeTest
 * Dump the rendered HTML to storage/smoke/ for inspection:   SMOKE_DUMP=1 php artisan test --filter=PagesSmokeTest
 */
beforeEach(function () {
    $this->admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@test.dev']);
    $this->admin->forceFill(['is_admin' => true])->save();

    $this->jane = User::factory()->create(['name' => 'Jane']);
    $this->admin->followers()->attach($this->jane->id);

    foreach ([['Lamp', 'Home', 4], ['Desk', 'Home', 40], ['Pen', 'Office', 0], ['Chair', 'Office', 12]] as $i => [$name, $cat, $stock]) {
        Product::create(['name' => $name, 'description' => 'Nice thing', 'price' => 10 * ($i + 1),
            'stock_quantity' => $stock, 'category' => $cat]);
    }
});

it('renders every page for an admin', function (string $path) {
    $res = $this->actingAs($this->admin)->get($path);

    if (env('SMOKE_DUMP')) {
        @mkdir(storage_path('smoke'), 0777, true);
        file_put_contents(storage_path('smoke/'.trim(preg_replace('/[^a-z0-9]+/i', '_', $path), '_').'.html'), $res->getContent());
    }

    expect($res->getStatusCode())->toBeLessThan(400);
})->with([
    '/', '/about', '/products',
    '/shoppingcart', '/shoppingcart/checkout',
    '/admin/dashboard', '/admin/users', '/admin/products', '/admin/products/create', '/admin/orders', '/orders',
    '/@Admin', '/@Admin/followers', '/profile', '/posts',
]);

it('redirects /admin to the dashboard', function () {
    $this->actingAs($this->admin)->get('/admin')->assertRedirect(route('admin.dashboard'));
});

it('renders the auth pages for guests', function (string $path) {
    $this->get($path)->assertOk();
})->with(['/login', '/register']);

it('keeps the admin area closed to non-admins and guests', function (string $path) {
    $this->actingAs($this->jane)->get($path)->assertForbidden();
    auth()->logout();
    $this->get($path)->assertRedirect('/login');
})->with(['/admin/dashboard', '/admin/users', '/admin/products', '/admin/orders']);

it('shows real numbers and four charts on the dashboard', function () {
    $html = $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk()->getContent();

    expect(substr_count($html, 'data-chart='))->toBe(4)
        ->and($html)->toContain('Total users')
        ->and($html)->toContain('Needs restocking')
        // 4 products: Lamp (4) and Chair (12 -> not low), Pen (0) => 1 low + 1 out
        ->and($html)->toContain('1 out of stock')
        ->and($html)->toContain('Jane');
});

it('passes page headers through the master layout', function () {
    $this->actingAs($this->admin)->get('/products')->assertOk()->assertSee('Welcome, Admin!');
});
