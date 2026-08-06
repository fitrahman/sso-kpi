<?php

namespace App\Services;

use App\Models\PassportClient;
use Illuminate\Support\Facades\Http;

class RoleDiscoveryService
{
    /**
     * Synchronize roles from the client's discovery URL
     */
    public function syncRoles(PassportClient $client): bool
    {
        if (empty($client->discovery_url)) {
            return false;
        }

        try {
            // Perform HTTP GET with timeout of 5 seconds
            $response = Http::withHeaders([
                'X-SSO-Secret' => $client->discovery_secret,
            ])->timeout(5)->get($client->discovery_url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['roles']) && is_array($data['roles'])) {
                    // Extract keys from roles array
                    $rolesArray = [];
                    foreach ($data['roles'] as $roleItem) {
                        if (isset($roleItem['key'])) {
                            $rolesArray[] = trim($roleItem['key']);
                        }
                    }

                    if (! empty($rolesArray)) {
                        $client->supported_roles = json_encode(array_values(array_unique($rolesArray)));
                        $client->roles_synced_at = now();
                        $client->save();

                        return true;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silent failure, do not overwrite old data
        }

        return false;
    }
}
