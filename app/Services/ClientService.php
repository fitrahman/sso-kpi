<?php

namespace App\Services;

use App\Models\ApplicationActivityLog;
use App\Models\PassportClient;
use App\Models\User;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientService
{
    /**
     * Create client application
     */
    public function createClient(array $validatedData, $logoFile = null)
    {
        $admin = Auth::user();
        $secret = Str::random(40);

        $rolesArray = [];
        if (! empty($validatedData['supported_roles'])) {
            $rolesArray = array_map('trim', explode(',', $validatedData['supported_roles']));
            $rolesArray = array_values(array_filter($rolesArray));
        }
        if (empty($rolesArray)) {
            $rolesArray = ['Admin', 'Staff'];
        }

        $logoPath = null;
        if ($logoFile) {
            $logoPath = $logoFile->store('app-logos', 'public');
        }

        $client = PassportClient::create([
            'name' => $validatedData['name'],
            'secret' => $secret,
            'redirect' => $validatedData['redirect'],
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
            'is_maintenance' => 0,
            'is_visible' => isset($validatedData['is_visible']) ? (bool) $validatedData['is_visible'] : true,
            'description' => $validatedData['description'] ?? null,
            'display_order' => $validatedData['display_order'] ?? 0,
            'supported_roles' => json_encode($rolesArray),
            'logo_path' => $logoPath,
        ]);

        // Create Webhook Endpoint if supplied
        if (! empty($validatedData['webhook_url'])) {
            WebhookEndpoint::create([
                'oauth_client_id' => $client->id,
                'url' => $validatedData['webhook_url'],
                'secret' => $validatedData['webhook_secret'] ?? null,
                'is_active' => isset($validatedData['webhook_active']) ? (bool) $validatedData['webhook_active'] : true,
            ]);
        }

        // Auto-grant access to all approved users in bulk
        $approvedUserIds = User::where('status', 'approved')->pluck('id');
        if ($approvedUserIds->isNotEmpty()) {
            $now = now();
            $accessRecords = $approvedUserIds->map(fn ($userId) => [
                'user_id' => $userId,
                'client_id' => $client->id,
                'status' => 'approved',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ])->toArray();

            DB::table('client_user_access')->insert($accessRecords);
        }

        ApplicationActivityLog::create([
            'oauth_client_id' => $client->id,
            'admin_id' => $admin->id,
            'action' => 'created',
            'description' => "Aplikasi baru '{$client->name}' berhasil ditambahkan (Client ID: {$client->id}).",
        ]);

        return [
            'client' => $client,
            'secret' => $secret,
        ];
    }

    /**
     * Update client application
     */
    public function updateClient(PassportClient $client, array $validatedData, $logoFile = null)
    {
        $admin = Auth::user();
        $changes = [];

        if ($client->name !== $validatedData['name']) {
            $changes[] = "Nama: '{$client->name}' → '{$validatedData['name']}'";
        }

        if ($client->redirect !== $validatedData['redirect']) {
            $changes[] = "Redirect URL: '{$client->redirect}' → '{$validatedData['redirect']}'";
        }

        $rolesArray = [];
        if (! empty($validatedData['supported_roles'])) {
            $rolesArray = array_map('trim', explode(',', $validatedData['supported_roles']));
            $rolesArray = array_values(array_filter($rolesArray));
        }
        if (empty($rolesArray)) {
            $rolesArray = ['Admin', 'Staff', 'pengguna'];
        }

        $client->name = $validatedData['name'];
        $client->redirect = $validatedData['redirect'];
        $client->description = $validatedData['description'] ?? null;
        $client->supported_roles = json_encode($rolesArray);
        $client->maintenance_message = $validatedData['maintenance_message'] ?? null;
        $client->display_order = $validatedData['display_order'] ?? 0;
        $client->is_visible = isset($validatedData['is_visible']) ? (bool) $validatedData['is_visible'] : true;

        if ($logoFile) {
            if ($client->logo_path) {
                Storage::disk('public')->delete($client->logo_path);
            }
            $path = $logoFile->store('app-logos', 'public');
            $client->logo_path = $path;
            $changes[] = 'Logo diperbarui';
        }

        $client->save();

        // Manage Webhook Endpoint
        if (! empty($validatedData['webhook_url'])) {
            $webhook = WebhookEndpoint::updateOrCreate(
                ['oauth_client_id' => $client->id],
                [
                    'url' => $validatedData['webhook_url'],
                    'secret' => $validatedData['webhook_secret'] ?? null,
                    'is_active' => isset($validatedData['webhook_active']) ? (bool) $validatedData['webhook_active'] : true,
                ]
            );
            $changes[] = 'Konfigurasi Webhook diperbarui';
        } else {
            // Delete webhook endpoint if URL is cleared
            WebhookEndpoint::where('oauth_client_id', $client->id)->delete();
        }

        if (! empty($changes)) {
            ApplicationActivityLog::create([
                'oauth_client_id' => $client->id,
                'admin_id' => $admin->id,
                'action' => 'updated',
                'description' => implode(', ', $changes),
            ]);
        }

        return $client;
    }

    /**
     * Toggle maintenance mode
     */
    public function toggleMaintenance(PassportClient $client)
    {
        $client->is_maintenance = ! $client->is_maintenance;
        $client->save();

        ApplicationActivityLog::create([
            'oauth_client_id' => $client->id,
            'admin_id' => Auth::id(),
            'action' => $client->is_maintenance ? 'maintenance_on' : 'maintenance_off',
            'description' => $client->is_maintenance
                ? "Mode pemeliharaan diaktifkan untuk '{$client->name}'"
                : "Mode pemeliharaan dinonaktifkan untuk '{$client->name}'",
        ]);

        return $client;
    }

    /**
     * Toggle client dashboard visibility
     */
    public function toggleVisibility(PassportClient $client)
    {
        $client->is_visible = ! $client->is_visible;
        $client->save();

        ApplicationActivityLog::create([
            'oauth_client_id' => $client->id,
            'admin_id' => Auth::id(),
            'action' => $client->is_visible ? 'visibility_on' : 'visibility_off',
            'description' => $client->is_visible
                ? "Aplikasi '{$client->name}' ditampilkan kembali di dashboard"
                : "Aplikasi '{$client->name}' disembunyikan dari dashboard",
        ]);

        return $client;
    }

    /**
     * Delete client application
     */
    public function deleteClient(PassportClient $client)
    {
        if ($client->logo_path) {
            Storage::disk('public')->delete($client->logo_path);
        }

        // Clean up related DB records
        DB::table('client_user_access')->where('client_id', $client->id)->delete();
        DB::table('user_client_roles')->where('oauth_client_id', $client->id)->delete();
        DB::table('application_activity_logs')->where('oauth_client_id', $client->id)->delete();
        WebhookEndpoint::where('oauth_client_id', $client->id)->delete();

        $client->delete();
    }

    /**
     * Delete client logo
     */
    public function deleteClientLogo(PassportClient $client)
    {
        if (! $client->logo_path) {
            throw new \Exception('Aplikasi ini tidak memiliki gambar.');
        }

        Storage::disk('public')->delete($client->logo_path);
        $client->logo_path = null;
        $client->save();

        ApplicationActivityLog::create([
            'oauth_client_id' => $client->id,
            'admin_id' => Auth::id(),
            'action' => 'logo_deleted',
            'description' => "Gambar kartu aplikasi '{$client->name}' dihapus.",
        ]);
    }
}
