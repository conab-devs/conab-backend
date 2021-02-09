<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Message",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *    ),
 *    @OA\Property(
 *        property="content",
 *        type="number",
 *        description="Texto da mensagem"
 *    ),
 *    @OA\Property(
 *        property="source_id",
 *        type="integer",
 *        description="Id do usuário que mandou a mensagem"
 *    ),
 *    @OA\Property(
 *        property="destination_id",
 *        type="integer",
 *        description="Id do usuário destino da mensagem"
 *    ),
 *    @OA\Property(
 *        property="cooperative_id",
 *        type="integer",
 *        description="Id da cooperativa"
 *    )
 * )
 */
class Message extends Model
{
    protected $fillable = [
        'content',
        'source_id',
        'destination_id',
        'cooperative_id'
    ];

    public function cooperative()
    {
        $this->belongsTo('App\Cooperative', 'cooperative_id');
    }

    public function source()
    {
        $this->belongsTo('App\User', 'source_id');
    }

    public function destination()
    {
        $this->belongsTo('App\User', 'destination_id');
    }
}
