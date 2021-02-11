<?php

use Illuminate\Support\Facades\Broadcast;
use App\Message;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('message.{id}', function ($user, $id) {
    $message = Message::find($id);
    $cooperativeUser = $message
        ->cooperative()
        ->first()
        ->users()
        ->where('id', $user->id)
        ->first();
    return (int) $user->id === (int) $message->user_id || filled($cooperativeUser);
});
