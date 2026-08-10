<?php

namespace App\Services;

use App\Jobs\DispatchSSOWebhookJob;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use App\Models\PassportClient;
use App\Models\User;
use App\Models\UserClientRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * Get paginated users with search & filters
     */
    public function getPaginatedUsers(array $filters, int $perPage = 10)
    {
        $query = User::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderByRaw("CASE 
            WHEN status = 'pending' THEN 1 
            WHEN status = 'approved' THEN 2 
            WHEN status = 'inactive' THEN 3 
            ELSE 4 END ASC")
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Update user details and client access/roles
     */
    public function updateUser(User $user, array $validatedData, array $clientIds, array $clientRoles)
    {
        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'] ?? null,
            'role' => $validatedData['role'],
        ];

        if ($user->role !== 'admin' && $user->id !== Auth::id()) {
            if (isset($validatedData['status'])) {
                $updateData['status'] = $validatedData['status'];

                if ($validatedData['status'] === 'inactive') {
                    $this->revokeUserTokens($user);

                    // Dispatch webhook for user deactivation
                    $assignedClients = $user->accessedClients()->get();
                    foreach ($assignedClients as $assignedClient) {
                        $clientModel = PassportClient::find($assignedClient->id);
                        if ($clientModel && $clientModel->webhookEndpoint && $clientModel->webhookEndpoint->is_active) {
                            DispatchSSOWebhookJob::dispatch(
                                $clientModel->webhookEndpoint->url,
                                $clientModel->webhookEndpoint->secret,
                                [
                                    'event' => 'user.access_revoked',
                                    'data' => [
                                        'user_id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'role' => 'none',
                                    ],
                                ]
                            );
                        }
                    }
                }
            }
        }

        $user->update($updateData);

        // Get all valid clients
        $allClients = PassportClient::where('personal_access_client', 0)
            ->where('password_client', 0)
            ->pluck('id')
            ->toArray();

        $validSelectedClientIds = array_intersect($clientIds, $allClients);

        // Remove unselected client access
        $unselectedClients = $user->accessedClients()->wherePivotNotIn('client_id', $validSelectedClientIds)->get();
        foreach ($unselectedClients as $unselectedClient) {
            $clientModel = PassportClient::find($unselectedClient->id);
            if ($clientModel && $clientModel->webhookEndpoint && $clientModel->webhookEndpoint->is_active) {
                DispatchSSOWebhookJob::dispatch(
                    $clientModel->webhookEndpoint->url,
                    $clientModel->webhookEndpoint->secret,
                    [
                        'event' => 'user.access_revoked',
                        'data' => [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => 'none',
                        ],
                    ]
                );
            }
            // Instead of detaching, update status and is_active to false
            $user->accessedClients()->updateExistingPivot($unselectedClient->id, [
                'status' => 'rejected',
                'is_active' => false,
            ]);
        }

        // Map roles for selected clients
        foreach ($validSelectedClientIds as $cId) {
            $existing = $user->accessedClients()->where('client_id', $cId)->first();
            if ($existing) {
                $user->accessedClients()->updateExistingPivot($cId, [
                    'status' => 'approved',
                    'is_active' => true,
                ]);
            } else {
                $user->accessedClients()->attach($cId, [
                    'status' => 'approved',
                    'is_active' => true,
                ]);
            }

            $clientModel = PassportClient::find($cId);
            $supportedRoles = [];
            if ($clientModel && ! empty($clientModel->supported_roles)) {
                $supportedRoles = json_decode($clientModel->supported_roles, true);
            }
            if (empty($supportedRoles) || ! is_array($supportedRoles)) {
                $supportedRoles = ['admin', 'pengguna'];
            }

            $roleValue = $clientRoles[$cId] ?? $supportedRoles[0] ?? 'user';

            if (! RoleValidationService::isValidRole($cId, $roleValue)) {
                throw new \InvalidArgumentException("Role '{$roleValue}' tidak valid untuk aplikasi ID {$cId}.");
            }

            UserClientRole::updateOrCreate(
                ['user_id' => $user->id, 'oauth_client_id' => $cId],
                ['role' => $roleValue]
            );

            // Dispatch webhook if client has configured active webhook
            if ($clientModel && $clientModel->webhookEndpoint && $clientModel->webhookEndpoint->is_active) {
                DispatchSSOWebhookJob::dispatch(
                    $clientModel->webhookEndpoint->url,
                    $clientModel->webhookEndpoint->secret,
                    [
                        'event' => 'user.role_updated',
                        'data' => [
                            'user_id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'role' => $roleValue,
                        ],
                    ]
                );
            }
        }

        return $user;
    }

    /**
     * Approve user
     */
    public function approveUser(User $user)
    {
        if ($user->status !== 'pending') {
            throw new \Exception('User ini bukan berstatus pending.');
        }

        $user->status = 'approved';
        $user->save();

        try {
            Mail::to($user->email)->send(new UserApprovedMail($user));
        } catch (\Exception $e) {
            // Silence mail error
        }

        return $user;
    }

    /**
     * Reject user (Deletes user and sends email)
     */
    public function rejectUser(User $user)
    {
        if ($user->status !== 'pending') {
            throw new \Exception('User ini bukan berstatus pending.');
        }

        try {
            Mail::to($user->email)->send(new UserRejectedMail($user));
        } catch (\Exception $e) {
            // Silence mail error
        }

        $user->delete();
    }

    /**
     * Delete user permanently
     */
    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            throw new \Exception('Akun administrator utama tidak dapat dihapus.');
        }
        if ($user->id === Auth::id()) {
            throw new \Exception('Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->revokeUserTokens($user);

        $user->delete();
    }

    /**
     * Revoke all OAuth tokens for a user
     */
    public function revokeUserTokens(User $user): void
    {
        $user->tokens()->each(function ($token) {
            $token->revoke();
        });
    }
}
