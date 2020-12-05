<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="obejct",
 *     @OA\Property(
 *        property="total_price",
 *        description="Preço total dos items dentro do carrinho de compras"
 *    ),
 *    @OA\Property(
 *        property="closed_at"
 *    ),
 *    @OA\Property(
 *        property="is_closed"
 *    ),
 *    @OA\Property(
 *        property="user_id",
 *        description="ID do usuário dono do carrinho de compras"
 *    )
 * )
 */
class Cart extends Model
{
    //
}
