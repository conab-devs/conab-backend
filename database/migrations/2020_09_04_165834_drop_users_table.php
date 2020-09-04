<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class dropUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_phones');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('product_carts');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_picture')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cpf')->unique();
            $table->enum('user_type', ['CUSTOMER', 'ADMIN_CONAB', 'ADMIN_COOP'])->default('CUSTOMER');
            $table->foreignId('cooperative_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

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
            $table->dateTime('delivered_at');
            $table->enum('unit_of_measure', ['kg', 'unit']);
            $table->foreignId('product_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('cart_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('address_id')->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });

        Schema::create('user_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('phone_id')->constrained()->onDelete('CASCADE');
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
        Schema::dropIfExists('users');

        Schema::table('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_picture');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cpf')->unique();
            $table->enum('user_type', ['CUSTOMER', 'ADMIN_CONAB', 'ADMIN_COOP'])->default('CUSTOMER');
            $table->foreignId('cooperative_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->timestamps();
        });
    }
}
