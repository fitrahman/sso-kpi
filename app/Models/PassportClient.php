<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClient;

class PassportClient extends BaseClient
{
    protected $casts = [
        'roles_synced_at' => 'datetime',
    ];

    /**
     * Determine if the client should skip the authorization prompt.
     *
     * @return bool
     */
    public function skipsAuthorization()
    {
        return true;
    }

    /**
     * Relasi ke WebhookEndpoint
     */
    public function webhookEndpoint()
    {
        return $this->hasOne(WebhookEndpoint::class, 'oauth_client_id');
    }
}
