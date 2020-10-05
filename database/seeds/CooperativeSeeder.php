<?php

use Illuminate\Database\Seeder;

class CooperativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Cooperative::class, 5)
            ->create()
            ->each(function ($cooperative) {
                 $phones = factory(App\Phone::class, 3)->create();
                 foreach ($phones as $phone)
                    $cooperative->phones()->attach($phone->id);
            });
    }
}
