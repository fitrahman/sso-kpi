<?php

namespace App\Models;

use Laravel\Passport\Client as BaseClient;

class PassportClient extends BaseClient
{
    protected $fillable = [
        'name',
        'secret',
        'redirect',
        'personal_access_client',
        'password_client',
        'revoked',
        'is_maintenance',
        'is_visible',
        'description',
        'display_order',
        'supported_roles',
        'logo_path',
        'maintenance_message',
        'discovery_url',
        'discovery_secret',
        'roles_synced_at',
    ];

    protected $casts = [
        'roles_synced_at' => 'datetime',
        'is_maintenance' => 'boolean',
        'is_visible' => 'boolean',
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

    /**
     * Relasi pengguna aktif yang memiliki akses
     */
    public function activeUsers()
    {
        return $this->belongsToMany(User::class, 'client_user_access', 'client_id', 'user_id')
            ->wherePivot('is_active', true);
    }
}
