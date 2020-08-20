<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_carts', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 8, 2)->nullable()->default(0.0);
            $table->decimal('price', 8, 2)->nullable()->default(0.0);
            $table->dateTime('delivered_at');
            $table->enum('unit_of_measure', ['kg', 'unit']);
            $table->foreignId('product_id')->constrained()->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('cart_id')->constrained()->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('product_carts');
    }
}
