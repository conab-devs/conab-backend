<?php

use Illuminate\Database\Seeder;

class CooperativeSeeder extends Seeder
{
    public function run()
    {
        factory(\App\Cooperative::class, 5)
            ->create()
            ->each(function ($cooperative) {
                 $phones = factory(App\Phone::class, 3)->create();
                 foreach ($phones as $phone)
                    $cooperative->phones()->attach($phone->id);

                 factory(\App\User::class, 1)->create([
                     'cooperative_id' => $cooperative->id,
                     'password' => '12345678',
                     'user_type' => 'ADMIN_COOP'
                 ]);

                 factory(\App\Product::class, 10)->create([
                     'category_id' => factory(\App\Category::class)->create()->id,
                     'cooperative_id' => $cooperative->id
                 ]);
            });
    }
}
