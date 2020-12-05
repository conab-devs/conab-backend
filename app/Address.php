<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="obejct",
 *     @OA\Property(
 *        property="street",
 *        description="Rua do endereço"
 *    ),
 *    @OA\Property(
 *        property="neighborhood",
 *        description="Bairro do endereço"
 *    ),
 *    @OA\Property(
 *        property="city",
 *        description="Cidade do endereço"
 *    ),
 *    @OA\Property(
 *        property="number",
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
