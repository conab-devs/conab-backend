<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUnnecessaryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('messages');

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('user_id')->nullable()->constrained();
            $table->foreignId('cooperative_id')->nullable()->constrained();
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
        Schema::dropIfExists('messages');
        
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->unsignedBigInteger('source_id');
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->foreignId('cooperative_id')->nullable()->constrained()->onDelete('CASCADE');
            $table->timestamps();

            $table->foreign('source_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('destination_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }
}
