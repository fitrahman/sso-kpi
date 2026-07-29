<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClientRole extends Model
{
    protected $fillable = [
        'user_id',
        'oauth_client_id',
        'role',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke OauthClient (Laravel Passport Client)
     */
    public function oauthClient()
    {
        return $this->belongsTo(\Laravel\Passport\Client::class, 'oauth_client_id');
    }
}
