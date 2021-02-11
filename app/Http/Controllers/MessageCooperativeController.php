<?php

namespace App\Http\Controllers;

use App\Events\Chat\SendMessage;
use App\Http\Requests\Message\StoreRequest;
use App\Message;
use Illuminate\Http\Request;

class MessageCooperativeController extends Controller
{
    public function index()
    {
        $conversations = auth()->user()
            ->cooperative()
            ->messages()
            ->orderBy('user_id')
            ->get();

        return response()->json($conversations);
    }

    public function store(StoreRequest $request)
    {
        $cooperative_id = auth()->user()->cooperative()->id;

        $message = new Message();
        $message->fill($request->all());
        $message->cooperative_id = $cooperative_id;
        $message->save();

        SendMessage::dispatch($message);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
