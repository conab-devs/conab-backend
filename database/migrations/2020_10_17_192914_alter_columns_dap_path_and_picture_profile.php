<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnsDapPathAndPictureProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('profile_picture')->change();
        });

        Schema::table('cooperatives', function (Blueprint $table) {
            $table->text('dap_path')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cooperatives', function (Blueprint $table) {
            $table->string('dap_path')->unique()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture');
        });
    }
}
