<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $fillable = [
        'oauth_client_id',
        'url',
        'secret',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke OauthClient (Laravel Passport Client)
     */
    public function oauthClient()
    {
        return $this->belongsTo(\Laravel\Passport\Client::class, 'oauth_client_id');
    }
}
