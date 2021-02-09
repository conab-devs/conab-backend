<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

/** @author Messages */
class MessageControllerTest extends TestCase
{
    use RefreshDatabase;
    
    //  TODO
    //  * O cliente deve poder criar mensagem.
    //  * Deve checar se o pedido está aberto.
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
}
