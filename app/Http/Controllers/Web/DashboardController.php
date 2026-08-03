<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ApplicationActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserApprovedMail;
use App\Mail\UserRejectedMail;
use Laravel\Passport\Client;
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
            $user->load('clientRoles');

            $approvedApps = $user->accessedClients()
                ->where('client_user_access.status', 'approved')
                ->get();

            // Load all visible clients ordered by display_order for the dashboard
            $allClients = Client::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();

            return view('dashboard', [
                'user'         => $user,
                'approvedApps' => $approvedApps,
                'allClients'   => $allClients,
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
            $search = $request->get('search');
            $sort = $request->get('sort');
            $direction = $request->get('direction', 'asc');

            if (!in_array(strtolower($direction), ['asc', 'desc'])) {
                $direction = 'asc';
            }

            $usersQuery = User::query();

            // Handle search
            if (!empty($search)) {
                $usersQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }

            // Handle sorting
            if ($sort === 'role') {
                $usersQuery->orderBy('role', $direction);
            } elseif ($sort === 'status') {
                $usersQuery->orderBy('status', $direction);
            } elseif ($sort === 'created_at') {
                $usersQuery->orderBy('created_at', $direction);
            } else {
                // Default: pending (menunggu) first, then approved (aktif), then inactive (nonaktif)
                $usersQuery->orderByRaw("CASE 
                    WHEN status = 'pending' THEN 1 
                    WHEN status = 'approved' THEN 2 
                    WHEN status = 'inactive' THEN 3 
                    ELSE 4 END ASC")
                    ->orderBy('created_at', 'desc');
            }

            $users = $usersQuery->paginate(10)->withQueryString();
            $totalCount = User::count();
            $pendingCount = User::where('status', 'pending')->count();
            $inactiveCount = User::where('status', 'inactive')->count();
 
            return view('admin.users', [
                'users'         => $users,
                'totalCount'    => $totalCount,
                'pendingCount'  => $pendingCount,
                'inactiveCount' => $inactiveCount,
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
                
                // Fetch the client model to validate its supported roles dynamically
                $clientModel = Client::find($cId);
                $supportedRoles = [];
                if ($clientModel && !empty($clientModel->supported_roles)) {
                    $supportedRoles = json_decode($clientModel->supported_roles, true);
                }
                if (empty($supportedRoles) || !is_array($supportedRoles)) {
                    $supportedRoles = ['admin', 'pengguna'];
                }

                // Save or update client role
                $roleValue = $inputRoles[$cId] ?? $supportedRoles[0];
                if (!in_array($roleValue, $supportedRoles)) {
                    $roleValue = $supportedRoles[0];
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






    // =========================================================
    // Application Management (Admin only)
    // =========================================================

    public function clients()
    {
        try {
            $clients = Client::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();

            // Attach user count per client
            $clients->each(function ($client) {
                $client->user_count = \DB::table('client_user_access')
                    ->where('client_id', $client->id)
                    ->where('status', 'approved')
                    ->count();
            });

            $activityLogs = ApplicationActivityLog::with('admin')
                ->orderByDesc('created_at')
                ->limit(30)
                ->get();

            return view('admin.apps', [
                'clients'      => $clients,
                'activityLogs' => $activityLogs,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat manajemen aplikasi: ' . $e->getMessage()]);
        }
    }

    public function updateClient(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            $admin  = Auth::user();
            $changes = [];

            $validated = $request->validate([
                'name'                => 'required|string|max:255',
                'description'         => 'nullable|string|max:500',
                'maintenance_message' => 'nullable|string|max:500',
                'display_order'       => 'nullable|integer|min:0',
                'is_visible'          => 'nullable|boolean',
                'logo'                => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            ]);

            if ($client->name !== $validated['name']) {
                $changes[] = "Nama: '{$client->name}' → '{$validated['name']}'";
            }

            $client->name                = $validated['name'];
            $client->description         = $validated['description'] ?? null;
            $client->maintenance_message = $validated['maintenance_message'] ?? null;
            $client->display_order       = $validated['display_order'] ?? 0;
            $client->is_visible          = $request->boolean('is_visible', true);

            if ($request->hasFile('logo')) {
                if ($client->logo_path) {
                    Storage::disk('public')->delete($client->logo_path);
                }
                $path = $request->file('logo')->store('app-logos', 'public');
                $client->logo_path = $path;
                $changes[] = 'Logo diperbarui';
            }

            $client->save();

            if (!empty($changes)) {
                ApplicationActivityLog::create([
                    'oauth_client_id' => $client->id,
                    'admin_id'        => $admin->id,
                    'action'          => 'updated',
                    'description'     => implode(', ', $changes),
                ]);
            }

            return back()->with('success', "Aplikasi '{$client->name}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui aplikasi: ' . $e->getMessage()]);
        }
    }

    public function toggleMaintenance($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->is_maintenance = !$client->is_maintenance;
            $client->save();

            ApplicationActivityLog::create([
                'oauth_client_id' => $client->id,
                'admin_id'        => Auth::id(),
                'action'          => $client->is_maintenance ? 'maintenance_on' : 'maintenance_off',
                'description'     => $client->is_maintenance
                    ? "Mode maintenance diaktifkan untuk '{$client->name}'"
                    : "Mode maintenance dinonaktifkan untuk '{$client->name}'",
            ]);

            return back()->with('success', $client->is_maintenance
                ? "Aplikasi '{$client->name}' sekarang dalam mode maintenance."
                : "Aplikasi '{$client->name}' kembali aktif."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah status maintenance: ' . $e->getMessage()]);
        }
    }

    public function toggleVisibility($id)
    {
        try {
            $client = Client::findOrFail($id);
            $client->is_visible = !$client->is_visible;
            $client->save();

            ApplicationActivityLog::create([
                'oauth_client_id' => $client->id,
                'admin_id'        => Auth::id(),
                'action'          => $client->is_visible ? 'visibility_on' : 'visibility_off',
                'description'     => $client->is_visible
                    ? "Aplikasi '{$client->name}' ditampilkan kembali di dashboard"
                    : "Aplikasi '{$client->name}' disembunyikan dari dashboard",
            ]);

            return back()->with('success', $client->is_visible
                ? "Aplikasi '{$client->name}' kini tampil di dashboard."
                : "Aplikasi '{$client->name}' disembunyikan dari dashboard."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah visibilitas: ' . $e->getMessage()]);
        }
    }

    public function deleteClientLogo($id)
    {
        try {
            $client = Client::findOrFail($id);

            if (!$client->logo_path) {
                return back()->withErrors(['error' => 'Aplikasi ini tidak memiliki gambar.']);
            }

            Storage::disk('public')->delete($client->logo_path);
            $client->logo_path = null;
            $client->save();

            ApplicationActivityLog::create([
                'oauth_client_id' => $client->id,
                'admin_id'        => Auth::id(),
                'action'          => 'logo_deleted',
                'description'     => "Gambar kartu aplikasi '{$client->name}' dihapus.",
            ]);

            return back()->with('success', "Gambar aplikasi '{$client->name}' berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus gambar: ' . $e->getMessage()]);
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
