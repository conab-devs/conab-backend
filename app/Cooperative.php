<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Cooperative",
 *     type="object",
 *     @OA\Property(
 *        property="name",
 *        type="string",
 *        description="Nome da cooperative"
 *    ),
 *    @OA\Property(
 *        property="dap_path",
 *        type="string",
 *        description="URL do arquivo DAP"
 *    ),
 *    @OA\Property(
 *        property="address_id",
 *        type="integer",
 *        description="Id do endereço da cooperativa"
 *    )
 * )
 *
 * @OA\Schema(
 *     schema="CooperativeRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="dap_path",
 *         type="string",
 *         format="base64",
 *         description="Arquivo do DAP no format PDF"
 *     ),
 *     @OA\Property(
 *         property="city",
 *         type="string",
 *         description="Cidade da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="street",
 *         type="string",
 *         description="Rua da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="neighborhood",
 *         type="string",
 *         description="Bairro da cooperativa"
 *     ),
 *     @OA\Property(
 *         property="number",
 *         type="string",
 *         description="Número do endereço da cooperativa"
 *     ),
 * )
 */
class Cooperative extends Model
{
    protected $fillable = ['name', 'dap_path'];

    protected $appends = ['dap_url'];

    protected $hidden = ['dap_path'];

    public function phones()
    {
        return $this->belongsToMany('App\Phone', 'cooperative_phones');
    }

    public function address()
    {
        return $this->belongsTo('App\Address', 'address_id');
    }

    public function admins()
    {
        return $this->hasMany('App\User', 'cooperative_id');
    }

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function messages()
    {
        return $this->hasMany('App\Message');
    }

    public function getDapUrlAttribute()
    {
        if (App::environment(['local','testing'])) {
            return Storage::url($this->attributes['dap_path']);
        }

        return $this->attributes['dap_path'];
    }
}
