<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('product_carts');
        Schema::dropIfExists('carts');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('closed_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->decimal('discount', 8, 2)->default(0.0);
            $table->foreignId('order_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('product_carts', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 8, 2);
            $table->decimal('price', 8, 2);
            $table->dateTime('delivered_at')->nullable();
            $table->enum('unit_of_measure', ['kg', 'unit']);
            $table->foreignId('product_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('cart_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_carts');
        Schema::dropIfExists('carts');

        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price', 8, 2)->default(0.0);
            $table->dateTime('closed_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('product_carts', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 8, 2)->default(0.0);
            $table->decimal('price', 8, 2);
            $table->dateTime('delivered_at')->nullable();
            $table->enum('unit_of_measure', ['kg', 'unit']);
            $table->foreignId('product_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('cart_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });
    }
}
