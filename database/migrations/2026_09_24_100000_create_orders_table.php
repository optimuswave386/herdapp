<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('number')->unique();
                // Orders outlive the customer's account, so the link is nulled rather than cascaded.
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('status')->default('pending')->index();
                $table->decimal('total', 10, 2);
                $table->string('payment_method');
                $table->string('shipping_name');
                $table->string('shipping_address');
                $table->string('shipping_city');
                $table->string('shipping_postal_code', 20);
                $table->string('shipping_country');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->cascadeOnDelete();
                // Deleting a product must not erase what was sold, so the name and price are snapshotted.
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('product_name');
                $table->decimal('unit_price', 10, 2);
                $table->unsignedInteger('quantity');
                $table->decimal('line_total', 10, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
