<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cooperative;
use App\Message;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        [
            'cooperative_id' => $id, 
            'content' => $content
        ] = $request->all();

        $user = auth()->user();
        $cooperative = Cooperative::find($id);
            
        $message = new Message();
        $message->content = $content;
        $message->cooperative()->associate($cooperative);
        $message->user()->associate($user);

        $message->save();

    }
}
