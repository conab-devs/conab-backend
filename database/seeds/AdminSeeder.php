<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->create([
            'name' => 'Admin Conab',
            'email' => 'adminconab@email.com',
            'password' => '123456',
            'profile_picture' => '',
            'user_type' => 'ADMIN_CONAB'
        ]);
    }
}
