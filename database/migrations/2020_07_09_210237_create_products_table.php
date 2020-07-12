<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->dateTime('estimated_delivery_time')->default(0);
            $table->string('photo_path')->unique()->nullable(false);
            $table->decimal('price', 8, 2)->nullable(false);
            $table->string('name')->nullable(false);
            $table->foreignId('cooperative_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('category_id')->constrained();
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
        Schema::dropIfExists('products');
    }
}
