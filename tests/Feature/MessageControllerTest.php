<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use App\Events\Chat\SendMessage;

/** @author Messages */
class MessageControllerTest extends TestCase
{
    use RefreshDatabase;
    
    //  TODO
    //  - O cliente deve poder criar mensagem.
    //  - Deve checar se o pedido está aberto.
    //  * Deve fazer Dispatch da mensagem.
    /**
     * Create message between client and cooperative.
     * 
     * @test
     */
    public function create_message()
    {        
        $client = factory(User::class)->create([
            'user_type' => 'customer',
            'cooperative_id' => null,
        ]);

        factory(Order::class)->create([
            'user_id' => $client->id,
        ]);

        $cooperative = (factory(User::class)->create())->cooperative()->first();

        $this->actingAs($client)->post('/api/messages', [
            'content' => 'Olá, bom dia!',
            'cooperative_id' => $cooperative->id
        ]);
    
        $this->assertDatabaseHas('messages', [
            'user_id' => $client->id,
            'cooperative_id' => $cooperative->id,
            'content' => 'Olá, bom dia!',
        ]);
    }

    /**
     * Return 400 code if order is closed.
     * 
     * @test
     */
    public function ensure_that_message_is_not_created()
    {
        $order = factory(Order::class)->create([
            'closed_at' => Carbon::now(),
        ]);
        $client = $order->user()->first();

        $cooperative = (factory(User::class)->create())->cooperative()->first();

        $response = $this->actingAs($client)->post('/api/messages', [
            'content' => 'Olá, bom dia!',
            'cooperative_id' => $cooperative->id
        ]);
    
        $response->assertStatus(400);
    }

    /** 
     * Dispatch the message with the proper data and permissions
     * 
     * @test
     */
    public function dispatch_message()
    {
        Event::fake();

        $client = factory(User::class)->create([
            'user_type' => 'customer',
            'cooperative_id' => null,
        ]);

        factory(Order::class)->create([
            'user_id' => $client->id,
        ]);

        $cooperative = (factory(User::class)->create())->cooperative()->first();

        $this->actingAs($client)->post('/api/messages', [
            'content' => 'Olá, bom dia!',
            'cooperative_id' => $cooperative->id
        ]);

        Event::assertDispatched(function (SendMessage $event) use ($cooperative, $client) {
            return $event->message->cooperative_id === $cooperative->id
                && $event->message->user_id === $client->id;
        });
    }
}
