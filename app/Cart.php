<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     @OA\Property(
 *        property="total_price",
 *        type="float",
 *        description="Preço total dos items dentro do carrinho de compras"
 *    ),
 *    @OA\Property(
 *        property="closed_at",
 *        type="date-time"
 *    ),
 *    @OA\Property(
 *        property="is_closed",
 *        type="boolean"
 *    ),
 *    @OA\Property(
 *        property="user_id",
 *        type="integer",
 *        description="ID do usuário dono do carrinho de compras"
 *    )
 * )
 */
class Cart extends Model
{
    //
}
