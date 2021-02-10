<?php

use Illuminate\Database\Seeder;

class GlobalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = factory(\App\User::class)->create([
            'email' => 'client@client.com',
            'password' => 'client',
            'user_type' => 'CUSTOMER',
            'cooperative_id' => null,
        ]);

        $cooperativist = factory(\App\User::class)->create([
            'email' => 'cooperativista@cooperativista.com',
            'password' => 'cooperativista',
        ]);

        $cooperative = $cooperativist->cooperative()->first();

        $category = factory(\App\Category::class)->create([
            'name' => 'Frutas',
        ]);

        $products = factory(\App\Product::class, 10)->create([
            'category_id' => $category->id,
            'cooperative_id' => $cooperative->id,
        ]);

        $order = factory(\App\Order::class)->create([
            'user_id' => $client->id,
        ]);

        factory(\App\ProductCart::class)->make([
            'product_id' => $products->first()->id,
            'order_id' => $order->id,
        ])->save();
    }
}
