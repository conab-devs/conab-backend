<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     ),
 *     @OA\Property(
 *         property="cpf",
 *         type="string",
 *         description="CPF do usuário no formato XXX.XXX.XXX-XX"
 *     ),
 *     @OA\Property(
 *         property="user_type",
 *         type="string",
 *         description="Tipo do usuário (ADMIN_COOP, ADMIN_CONAB ou CUSTOMER)"
 *     ),
 *     @OA\Property(
 *         property="profile_picture",
 *         type="string",
 *         description="URL da foto de perfil do usuário"
 *     ),
 * )
 *
 * @OA\Schema(
 *     schema="UserStoreRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Senha do usuário"
 *     ),
 *     @OA\Property(
 *         property="cpf",
 *         type="string",
 *         description="CPF do usuário no formato XXX.XXX.XXX-XX"
 *     ),
 *     @OA\Property(
 *         property="profile_picture",
 *         type="string",
 *         description="URL da foto de perfil do usuário"
 *     ),
 * )
 *
 * @OA\Schema(
 *     schema="UserUpdateRequest",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nome do usuário"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email do usuário"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Senha do usuário"
 *     ),
 *     @OA\Property(
 *         property="new_password",
 *         type="string",
 *         description="Nova senha do usuário"
 *     ),
 *     @OA\Property(
 *         property="cpf",
 *         type="string",
 *         description="CPF do usuário no formato XXX.XXX.XXX-XX"
 *     ),
 *     @OA\Property(
 *         property="profile_picture",
 *         type="string",
 *         description="URL da foto de perfil do usuário"
 *     ),
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'cpf', 'user_type'
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'isProvider'
    ];

    public function setPasswordAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function getPasswordAttribute()
    {
        return $this->attributes['password'];
    }

    public function getProfilePictureAttribute(): ?string
    {
        if (App::environment('testing')
            || $this->attributes['profile_picture'] === null
        ) {
            return $this->attributes['profile_picture'];
        }

        if (App::environment('production')) {
            return $this->attributes['profile_picture'];
        }

        return Storage::url($this->attributes['profile_picture']);
    }

    public function getIsProviderAttribute()
    {
        return $this->cooperative()->first() !== null;
    }

    public function cooperative(): BelongsTo
    {
        return $this->belongsTo('App\Cooperative');
    }

    public function phones(): BelongsToMany
    {
        return $this->belongsToMany('App\Phone', 'user_phones');
    }

    public function addresses()
    {
        return $this->belongsToMany('App\Address', 'user_addresses');
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'user_id');
    }

    public function message()
    {
        return $this->hasMany('App\Message');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
