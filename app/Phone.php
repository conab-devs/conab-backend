<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Phone",
 *     type="object",
 *     @OA\Property(
 *        property="number",
 *        type="string",
 *        description="Número do telefone"
 *    )
 * )
 */
class Phone extends Model
{
    protected $fillable = ['number'];

    protected $hidden = ['created_at', 'updated_at', 'id'];

    public function cooperative()
    {
        return $this->belongsToMany('App\Cooperative', 'cooperative_phones');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'user_phones');
    }
}
