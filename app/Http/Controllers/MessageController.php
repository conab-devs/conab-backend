<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cooperative;
use App\Message;
use App\Events\Chat\SendMessage;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        [
            'cooperative_id' => $id, 
            'content' => $content
        ] = $request->all();

        $user = auth()->user();

        $order = $user->orders()->firstWhere('closed_at', null);
        
        if (! filled($order)) {
            return response()->json([
                'message' => 'Não há pedidos abertos',
            ], 400);
        }

        $cooperative = Cooperative::find($id);
            
        $message = new Message();
        $message->content = $content;
        $message->cooperative()->associate($cooperative);
        $message->user()->associate($user);

        $message->save();

        SendMessage::dispatch($message);
    }
}
