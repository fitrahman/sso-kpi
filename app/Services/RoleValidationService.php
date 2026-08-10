<?php

namespace App\Services;

use App\Models\PassportClient;

class RoleValidationService
{
    /**
     * Memeriksa apakah role valid untuk client_id tertentu.
     */
    public static function isValidRole($oauthClientId, string $role): bool
    {
        $roleTrimmed = strtolower(trim($role));
        if (in_array($roleTrimmed, ['user', 'pengguna'])) {
            return true;
        }

        $client = PassportClient::find($oauthClientId);
        if (! $client) {
            return false;
        }

        $supported = [];
        if (! empty($client->supported_roles)) {
            $supported = json_decode($client->supported_roles, true);
        }
        if (empty($supported) || ! is_array($supported)) {
            $supported = ['admin', 'pengguna'];
        }

        $supportedLower = array_map('strtolower', $supported);

        return in_array($roleTrimmed, $supportedLower);
    }
}
