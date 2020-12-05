<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     @OA\Property(
 *        property="street",
 *        type="string",
 *        description="Rua do endereço"
 *    ),
 *    @OA\Property(
 *        property="neighborhood",
 *        type="string",
 *        description="Bairro do endereço"
 *    ),
 *    @OA\Property(
 *        property="city",
 *        type="string",
 *        description="Cidade do endereço"
 *    ),
 *    @OA\Property(
 *        property="number",
 *        type="string",
 *        description="Número do endereço"
 *    )
 * )
 */
class Address extends Model
{
    protected $fillable = [
        'street', 'neighborhood', 'city', 'number'
    ];

    public function cooperative()
    {
        return $this->hasOne(Cooperative::class);
    }

    public function user()
    {
        return $this->hasOne('App\User', 'user_addresses');
    }
}
