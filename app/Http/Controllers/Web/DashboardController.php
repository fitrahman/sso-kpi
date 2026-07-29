<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use Laravel\Passport\Client;
use App\Models\ProfileUpdateRequest;
use App\Models\UserClientRole;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            $approvedApps = $user->accessedClients()
                ->where('client_user_access.status', 'approved')
                ->get();

            return view('dashboard', [
                'user'         => $user,
                'approvedApps' => $approvedApps,
            ]);

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => config('app.debug') ? 'Failed to load dashboard: ' . $e->getMessage() : 'Failed to load dashboard: Internal Server Error']);
        }
    }

    /**
     * Show all users (Admin only)
     */
    public function users(Request $request)
    {
        try {
            $status = $request->get('status', 'approved');
            
            if ($status === 'approved') {
                $users = User::whereIn('status', ['approved', 'inactive'])
                    ->orderByRaw("CASE WHEN status = 'approved' THEN 0 ELSE 1 END ASC")
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            } else {
                $users = User::where('status', 'pending')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            }

            $pendingCount = User::where('status', 'pending')->count();
            $approvedCount = User::whereIn('status', ['approved', 'inactive'])->count();

            return view('admin.users', [
                'users'         => $users,
                'status'        => $status,
                'pendingCount'  => $pendingCount,
                'approvedCount' => $approvedCount,
            ]);

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => config('app.debug') ? 'Failed to load users: ' . $e->getMessage() : 'Failed to load users: Internal Server Error']);
        }
    }

    /**
     * Show edit user form (Admin only)
     */
    public function editUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $roles = array_merge(['admin'], User::ROLES);
            
            // Get all OAuth clients (Sistem 1, Sistem 2, etc.) excluding personal and password clients
            $clients = Client::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->get();
                
            // Get the list of client IDs that this user currently has approved access to
            $userAccessIds = $user->accessedClients()
                ->where('client_user_access.status', 'approved')
                ->pluck('client_id')
                ->toArray();

            // Get the list of client roles mapped by client ID
            $userClientRoles = $user->clientRoles()
                ->pluck('role', 'oauth_client_id')
                ->toArray();
            
            return view('admin.edit_user', [
                'user'            => $user,
                'roles'           => $roles,
                'clients'         => $clients,
                'userAccessIds'   => $userAccessIds,
                'userClientRoles' => $userClientRoles,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->withErrors(['error' => config('app.debug') ? 'Failed to find user: ' . $e->getMessage() : 'Failed to find user: Internal Server Error']);
        }
    }

    /**
     * Update user details (Admin only)
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $rules = [
                'name'  => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'phone' => 'nullable|string|min:10|max:15',
                'role'  => 'required|in:admin,' . implode(',', User::ROLES),
            ];

            if ($user->role !== 'admin' && $user->id !== Auth::id()) {
                $rules['status'] = 'required|in:pending,approved,inactive';
            }

            $request->validate($rules);

            $updateData = [
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role'  => $request->role,
            ];

            if ($user->role !== 'admin' && $user->id !== Auth::id()) {
                $updateData['status'] = $request->status;

                if ($request->status === 'inactive') {
                    $user->tokens()->each(function ($token) {
                        $token->revoke();
                    });
                }
            }

            $user->update($updateData);

            // Sync client access from admin checkboxes
            $clientIds = $request->input('clients', []); // list of checked client IDs
            
            // Get all valid clients to prevent manipulating other client types
            $allClients = Client::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->pluck('id')
                ->toArray();
                
            $validSelectedClientIds = array_intersect($clientIds, $allClients);
            
            // 1. We want to delete any access records for clients NOT selected
            $user->accessedClients()->wherePivotNotIn('client_id', $validSelectedClientIds)->detach();

            // 2. Delete roles for clients NOT selected
            UserClientRole::where('user_id', $user->id)
                ->whereNotIn('oauth_client_id', $validSelectedClientIds)
                ->delete();
            
            // 3. For each selected client, we attach or update it with 'approved' status AND updateOrCreate role
            $inputRoles = $request->input('client_roles', []);

            foreach ($validSelectedClientIds as $cId) {
                $existing = $user->accessedClients()->where('client_id', $cId)->first();
                if ($existing) {
                    $user->accessedClients()->updateExistingPivot($cId, ['status' => 'approved']);
                } else {
                    $user->accessedClients()->attach($cId, ['status' => 'approved']);
                }

                // Save or update client role
                $roleValue = $inputRoles[$cId] ?? 'viewer';
                if (!in_array($roleValue, ['admin', 'editor', 'viewer'])) {
                    $roleValue = 'viewer';
                }

                UserClientRole::updateOrCreate(
                    ['user_id' => $user->id, 'oauth_client_id' => $cId],
                    ['role' => $roleValue]
                );
            }

            return redirect()->route('admin.users')
                ->with('success', 'Data pengguna berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => config('app.debug') ? 'Failed to update user: ' . $e->getMessage() : 'Failed to update user: Internal Server Error']);
        }
    }

    public function approveUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->status !== 'pending') {
                return back()->withErrors(['error' => 'User ini bukan berstatus pending.']);
            }

            $user->status = 'approved';
            $user->save();

            try {
                Mail::to($user->email)->send(new UserApprovedMail($user));
            } catch (\Exception $e) {
                // Biarkan lanjut meskipun email gagal
            }

            return back()->with('success', 'User ' . $user->email . ' berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => config('app.debug') ? 'Gagal menyetujui user: ' . $e->getMessage() : 'Gagal menyetujui user: Internal Server Error']);
        }
    }

    public function rejectUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->status !== 'pending') {
                return back()->withErrors(['error' => 'User ini bukan berstatus pending.']);
            }

            try {
                Mail::to($user->email)->send(new UserRejectedMail($user));
            } catch (\Exception $e) {
                // Biarkan lanjut meskipun email gagal
            }

            $user->delete();

            return back()->with('success', 'User berhasil ditolak dan dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => config('app.debug') ? 'Gagal menolak user: ' . $e->getMessage() : 'Gagal menolak user: Internal Server Error']);
        }
    }

    public function appGateway(Request $request)
    {
        $appName = $request->query('appName');
        if (!$appName) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Aplikasi tidak valid.']);
        }

        $client = Client::where('name', $appName)->first();
        if (!$client) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Aplikasi tidak ditemukan di sistem.']);
        }

        $user = Auth::user();
        
        // Admin has direct access to all portals
        if ($user->role === 'admin') {
            $parsedUrl = parse_url($client->redirect);
            $baseUrl = ($parsedUrl['scheme'] ?? 'http') . '://' . ($parsedUrl['host'] ?? '');
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':' . $parsedUrl['port'];
            }
            return redirect($baseUrl . '/login');
        }

        // Cek apakah user memiliki akses ke aplikasi ini (tabel client_user_access) dengan status approved
        $access = $user->accessedClients()
            ->where('client_id', $client->id)
            ->where('client_user_access.status', 'approved')
            ->first();

        if ($access) {
            // Beri akses -> Redirect ke halaman OAuth Authorize aplikasi terkait
            $parsedUrl = parse_url($client->redirect);
            $baseUrl = ($parsedUrl['scheme'] ?? 'http') . '://' . ($parsedUrl['host'] ?? '');
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':' . $parsedUrl['port'];
            }
            
            return redirect($baseUrl . '/login');
        }

        // Jika tidak memiliki akses approved, langsung tampilkan halaman Akses Ditolak
        return view('auth.app-denied', ['appName' => $appName]);
    }





    /**
     * Deactivate user account (Admin only)
     */
    public function deactivateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role === 'admin') {
                return back()->withErrors(['error' => 'Akun administrator utama tidak dapat dinonaktifkan.']);
            }
            if ($user->id === Auth::id()) {
                return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
            }

            $user->update(['status' => 'inactive']);

            // Revoke all of the user's OAuth access tokens
            $user->tokens()->each(function ($token) {
                $token->revoke();
            });

            return back()->with('success', 'Akun ' . $user->name . ' berhasil dinonaktifkan.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menonaktifkan akun: ' . $e->getMessage()]);
        }
    }

    /**
     * Activate user account (Admin only)
     */
    public function activateUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'approved']);

            return back()->with('success', 'Akun ' . $user->name . ' berhasil diaktifkan kembali.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengaktifkan akun: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete user account (Admin only)
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role === 'admin') {
                return back()->withErrors(['error' => 'Akun administrator utama tidak dapat dihapus.']);
            }
            if ($user->id === Auth::id()) {
                return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
            }

            // Revoke tokens
            $user->tokens()->each(function ($token) {
                $token->revoke();
            });

            // Delete the user (database cascade will handle profile_update_requests and client_user_access)
            $user->delete();

            return back()->with('success', 'Akun berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus akun: ' . $e->getMessage()]);
        }
    }
}
