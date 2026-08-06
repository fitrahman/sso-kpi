<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ApplicationActivityLog;
use App\Models\PassportClient;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\ClientService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $userService;

    protected $clientService;

    public function __construct(UserService $userService, ClientService $clientService)
    {
        $this->userService = $userService;
        $this->clientService = $clientService;
    }

    /**
     * Show dashboard
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $user->load('clientRoles');

            $approvedApps = $user->accessedClients()
                ->where('client_user_access.is_active', true)
                ->get();

            $allClients = PassportClient::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();

            return view('dashboard', [
                'user' => $user,
                'approvedApps' => $approvedApps,
                'allClients' => $allClients,
            ]);

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => config('app.debug') ? 'Failed to load dashboard: '.$e->getMessage() : 'Failed to load dashboard: Internal Server Error']);
        }
    }

    /**
     * Show all users (Admin only)
     */
    public function users(Request $request)
    {
        try {
            $filters = $request->only(['search', 'role', 'status']);
            $users = $this->userService->getPaginatedUsers($filters, 10);

            $totalCount = User::count();
            $pendingCount = User::where('status', 'pending')->count();
            $inactiveCount = User::where('status', 'inactive')->count();
            $rolesList = array_merge(['admin'], User::ROLES);

            return view('admin.users', [
                'users' => $users,
                'totalCount' => $totalCount,
                'pendingCount' => $pendingCount,
                'inactiveCount' => $inactiveCount,
                'search' => $filters['search'] ?? '',
                'roleFilter' => $filters['role'] ?? '',
                'statusFilter' => $filters['status'] ?? '',
                'rolesList' => $rolesList,
            ]);

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => config('app.debug') ? 'Failed to load users: '.$e->getMessage() : 'Failed to load users: Internal Server Error']);
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

            $clients = PassportClient::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->get();

            $userAccessIds = $user->accessedClients()
                ->where('client_user_access.is_active', true)
                ->pluck('client_id')
                ->toArray();

            $userClientRoles = $user->clientRoles()
                ->pluck('role', 'oauth_client_id')
                ->toArray();

            return view('admin.edit_user', [
                'user' => $user,
                'roles' => $roles,
                'clients' => $clients,
                'userAccessIds' => $userAccessIds,
                'userClientRoles' => $userClientRoles,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->withErrors(['error' => config('app.debug') ? 'Failed to find user: '.$e->getMessage() : 'Failed to find user: Internal Server Error']);
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
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                'phone' => 'nullable|string|min:10|max:15',
                'role' => 'required|in:admin,'.implode(',', User::ROLES),
            ];

            if ($user->role !== 'admin' && $user->id !== Auth::id()) {
                $rules['status'] = 'required|in:pending,approved,inactive';
            }

            $validated = $request->validate($rules);
            $clientIds = $request->input('clients', []);
            $clientRoles = $request->input('client_roles', []);

            $this->userService->updateUser($user, $validated, $clientIds, $clientRoles);

            return redirect()->route('admin.users')
                ->with('success', 'Data pengguna berhasil diperbarui!');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => config('app.debug') ? 'Failed to update user: '.$e->getMessage() : 'Failed to update user: Internal Server Error']);
        }
    }

    public function approveUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $this->userService->approveUser($user);

            return back()->with('success', 'User '.$user->email.' berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => config('app.debug') ? 'Gagal menyetujui user: '.$e->getMessage() : 'Gagal menyetujui user: Internal Server Error']);
        }
    }

    public function rejectUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $this->userService->rejectUser($user);

            return back()->with('success', 'User berhasil ditolak dan dihapus.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => config('app.debug') ? 'Gagal menolak user: '.$e->getMessage() : 'Gagal menolak user: Internal Server Error']);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $this->userService->deleteUser($user);

            return back()->with('success', 'Akun berhasil dihapus secara permanen.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus akun: '.$e->getMessage()]);
        }
    }

    public function appGateway(Request $request)
    {
        $appName = $request->query('appName');
        if (! $appName) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Aplikasi tidak valid.']);
        }

        $client = PassportClient::where('name', $appName)->first();
        if (! $client) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Aplikasi tidak ditemukan di sistem.']);
        }

        $user = Auth::user();

        if ($client->is_maintenance && $user->role !== 'admin') {
            return redirect()->route('app.maintenance', [
                'appName' => $client->name,
                'message' => $client->maintenance_message ?? 'Aplikasi sedang dalam pemeliharaan sistem.',
            ]);
        }

        if ($user->role === 'admin') {
            $parsedUrl = parse_url($client->redirect);
            $baseUrl = ($parsedUrl['scheme'] ?? 'http').'://'.($parsedUrl['host'] ?? '');
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':'.$parsedUrl['port'];
            }

            return redirect($baseUrl.'/login');
        }

        $access = $user->accessedClients()
            ->where('client_id', $client->id)
            ->where('client_user_access.is_active', true)
            ->first();

        if ($access) {
            $parsedUrl = parse_url($client->redirect);
            $baseUrl = ($parsedUrl['scheme'] ?? 'http').'://'.($parsedUrl['host'] ?? '');
            if (isset($parsedUrl['port'])) {
                $baseUrl .= ':'.$parsedUrl['port'];
            }

            return redirect($baseUrl.'/login');
        }

        return view('auth.app-denied', ['appName' => $appName]);
    }

    // =========================================================
    // Application Management (Admin only)
    // =========================================================

    public function stats()
    {
        try {
            $totalUsers = User::count();
            $pendingUsers = User::where('status', 'pending')->count();
            $activeClientsCount = PassportClient::where('personal_access_client', 0)->where('password_client', 0)->count();

            // Login success today
            $todayLogins = UserActivityLog::where('activity', 'login_success')
                ->whereDate('created_at', today())
                ->count();

            // Recent user activities
            $recentActivities = UserActivityLog::with('user')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();

            // Construct past 7 days dates and labels
            $days = [];
            $chartDays = [];
            for ($i = 6; $i >= 0; $i--) {
                $days[] = now()->subDays($i)->format('Y-m-d');
                $chartDays[] = now()->subDays($i)->format('d M');
            }

            // Get clients and query daily token counts (logins)
            $clients = PassportClient::where('personal_access_client', 0)
                ->where('password_client', 0)
                ->get();

            $appsChartData = $clients->map(function ($client) use ($days) {
                $dataPoints = [];
                foreach ($days as $date) {
                    $count = DB::table('oauth_access_tokens')
                        ->where('client_id', $client->id)
                        ->whereDate('created_at', $date)
                        ->count();
                    $dataPoints[] = $count;
                }

                return [
                    'name' => $client->name,
                    'data' => $dataPoints,
                ];
            });

            return view('admin.stats', [
                'totalUsers' => $totalUsers,
                'pendingUsers' => $pendingUsers,
                'activeClientsCount' => $activeClientsCount,
                'todayLogins' => $todayLogins,
                'recentActivities' => $recentActivities,
                'chartDays' => $chartDays,
                'appsChartData' => $appsChartData,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat statistik & audit: '.$e->getMessage()]);
        }
    }

    public function clients()
    {
        try {
            $clients = PassportClient::with('webhookEndpoint')->where('personal_access_client', 0)
                ->where('password_client', 0)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();

            $clients->each(function ($client) {
                $client->user_count = DB::table('client_user_access')
                    ->where('client_id', $client->id)
                    ->where('is_active', true)
                    ->count();
            });

            $activityLogs = ApplicationActivityLog::with('admin')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();

            return view('admin.apps', [
                'clients' => $clients,
                'activityLogs' => $activityLogs,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memuat manajemen aplikasi: '.$e->getMessage()]);
        }
    }

    public function storeClient(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'redirect' => 'required|url|max:500',
                'description' => 'nullable|string|max:500',
                'supported_roles' => 'nullable|string|max:500',
                'display_order' => 'nullable|integer|min:0',
                'is_visible' => 'nullable|boolean',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
                'webhook_url' => 'nullable|url|max:500',
                'webhook_secret' => 'nullable|string|max:255',
                'webhook_active' => 'nullable|boolean',
            ]);

            $result = $this->clientService->createClient($validated, $request->file('logo'));

            return back()
                ->with('success', "Aplikasi '{$result['client']->name}' berhasil ditambahkan!")
                ->with('new_client_name', $result['client']->name)
                ->with('new_client_id', $result['client']->id)
                ->with('new_client_secret', $result['secret']);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan aplikasi: '.$e->getMessage()]);
        }
    }

    public function updateClient(Request $request, $id)
    {
        try {
            $client = PassportClient::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'redirect' => 'required|url|max:500',
                'description' => 'nullable|string|max:500',
                'supported_roles' => 'nullable|string|max:500',
                'maintenance_message' => 'nullable|string|max:500',
                'display_order' => 'nullable|integer|min:0',
                'is_visible' => 'nullable|boolean',
                'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
                'webhook_url' => 'nullable|url|max:500',
                'webhook_secret' => 'nullable|string|max:255',
                'webhook_active' => 'nullable|boolean',
            ]);

            $this->clientService->updateClient($client, $validated, $request->file('logo'));

            return back()->with('success', "Aplikasi '{$client->name}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui aplikasi: '.$e->getMessage()]);
        }
    }

    public function toggleMaintenance($id)
    {
        try {
            $client = PassportClient::findOrFail($id);
            $this->clientService->toggleMaintenance($client);

            return back()->with('success', $client->is_maintenance
                ? "Aplikasi '{$client->name}' sekarang dalam mode maintenance."
                : "Aplikasi '{$client->name}' kembali aktif."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah status maintenance: '.$e->getMessage()]);
        }
    }

    public function toggleVisibility($id)
    {
        try {
            $client = PassportClient::findOrFail($id);
            $this->clientService->toggleVisibility($client);

            return back()->with('success', $client->is_visible
                ? "Aplikasi '{$client->name}' kini tampil di dashboard."
                : "Aplikasi '{$client->name}' disembunyikan dari dashboard."
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengubah visibilitas: '.$e->getMessage()]);
        }
    }

    public function deleteClient($id)
    {
        try {
            $client = PassportClient::findOrFail($id);
            $clientName = $client->name;
            $this->clientService->deleteClient($client);

            return redirect()->route('admin.clients')->with('success', "Aplikasi '{$clientName}' berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus aplikasi: '.$e->getMessage()]);
        }
    }

    public function deleteClientLogo($id)
    {
        try {
            $client = PassportClient::findOrFail($id);
            $this->clientService->deleteClientLogo($client);

            return back()->with('success', "Gambar aplikasi '{$client->name}' berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus gambar: '.$e->getMessage()]);
        }
    }

    /**
     * Show detail of an application along with all users and their local roles (Admin only)
     */
    public function clientUsers(Request $request, $id)
    {
        try {
            $client = PassportClient::findOrFail($id);
            $search = $request->get('search');
            $roleFilter = $request->get('role');
            $accessFilter = $request->get('access');
            $localRoleFilter = $request->get('local_role');

            $usersQuery = User::query();

            if (! empty($search)) {
                $usersQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('role', 'like', '%'.$search.'%');
                });
            }

            if (! empty($roleFilter)) {
                $usersQuery->where('role', $roleFilter);
            }

            if (! empty($accessFilter)) {
                if ($accessFilter === 'approved') {
                    $userIdsWithAccess = DB::table('client_user_access')
                        ->where('client_id', $client->id)
                        ->where('is_active', true)
                        ->pluck('user_id');
                    $usersQuery->whereIn('id', $userIdsWithAccess);
                } elseif ($accessFilter === 'no_access') {
                    $userIdsWithAccess = DB::table('client_user_access')
                        ->where('client_id', $client->id)
                        ->where('is_active', true)
                        ->pluck('user_id');
                    $usersQuery->whereNotIn('id', $userIdsWithAccess);
                }
            }

            if (! empty($localRoleFilter)) {
                if ($localRoleFilter === 'none') {
                    $userIdsWithRole = \App\Models\UserClientRole::where('oauth_client_id', $client->id)
                        ->whereNotNull('role')
                        ->where('role', '!=', '')
                        ->pluck('user_id');
                    $usersQuery->whereNotIn('id', $userIdsWithRole);
                } else {
                    $userIdsWithRole = \App\Models\UserClientRole::where('oauth_client_id', $client->id)
                        ->where('role', $localRoleFilter)
                        ->pluck('user_id');
                    $usersQuery->whereIn('id', $userIdsWithRole);
                }
            }

            $users = $usersQuery->orderBy('name')->paginate(10)->withQueryString();

            $accessMap = DB::table('client_user_access')
                ->where('client_id', $client->id)
                ->pluck('is_active', 'user_id')
                ->toArray();

            $localRolesMap = \App\Models\UserClientRole::where('oauth_client_id', $client->id)
                ->pluck('role', 'user_id')
                ->toArray();

            $logs = ApplicationActivityLog::where('oauth_client_id', $client->id)
                ->with('admin')
                ->orderBy('created_at', 'desc')
                ->take(30)
                ->get();

            $rolesList = array_merge(['admin'], User::ROLES);

            return view('admin.client_users', [
                'client' => $client,
                'users' => $users,
                'accessMap' => $accessMap,
                'localRolesMap' => $localRolesMap,
                'logs' => $logs,
                'search' => $search,
                'roleFilter' => $roleFilter,
                'accessFilter' => $accessFilter,
                'localRoleFilter' => $localRoleFilter,
                'rolesList' => $rolesList,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('admin.clients')
                ->withErrors(['error' => 'Gagal memuat detail pengguna aplikasi: '.$e->getMessage()]);
        }
    }

    /**
     * Update a user's access status and local role for a specific application (Admin only)
     */
    public function updateClientUser(Request $request, $clientId, $userId)
    {
        try {
            $client = PassportClient::findOrFail($clientId);
            $user = User::findOrFail($userId);

            $validated = $request->validate([
                'access_status' => 'nullable|in:approved,pending,rejected,none',
                'has_access' => 'nullable|boolean',
                'local_role' => 'nullable|string|max:50',
                'access_submitted' => 'nullable|boolean',
            ]);

            // Handle access status
            if ($request->has('access_submitted')) {
                $isActive = $request->boolean('has_access');
                $status = $isActive ? 'approved' : 'rejected';

                $exists = DB::table('client_user_access')
                    ->where('user_id', $user->id)
                    ->where('client_id', $client->id)
                    ->exists();

                if ($exists) {
                    DB::table('client_user_access')
                        ->where('user_id', $user->id)
                        ->where('client_id', $client->id)
                        ->update([
                            'status' => $status,
                            'is_active' => $isActive,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('client_user_access')->insert([
                        'user_id' => $user->id,
                        'client_id' => $client->id,
                        'status' => $status,
                        'is_active' => $isActive,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Handle local role (independent of access checkbox status)
            if ($request->has('local_role')) {
                $localRole = trim($request->input('local_role') ?: 'user');
                \App\Models\UserClientRole::updateOrCreate(
                    ['user_id' => $user->id, 'oauth_client_id' => $client->id],
                    ['role' => $localRole]
                );
            }

            // Fetch current role and access status for logging & webhook
            $currentAccessObj = DB::table('client_user_access')
                ->where('user_id', $user->id)
                ->where('client_id', $client->id)
                ->first();
            $currentStatus = $currentAccessObj && $currentAccessObj->is_active ? 'approved' : 'rejected';

            $currentRoleObj = \App\Models\UserClientRole::where('user_id', $user->id)
                ->where('oauth_client_id', $client->id)
                ->first();
            $currentRole = $currentRoleObj ? $currentRoleObj->role : 'user';

            ApplicationActivityLog::create([
                'oauth_client_id' => $client->id,
                'admin_id' => Auth::id(),
                'action' => 'user_access_updated',
                'description' => "Akses '{$user->name}' diubah (Status: {$currentStatus}, Role: {$currentRole})",
            ]);

            if ($client->redirect) {
                $webhookUrl = str_replace('/auth/sso/callback', '/api/sso/webhook', $client->redirect);
                try {
                    \Illuminate\Support\Facades\Http::timeout(2)->post($webhookUrl, [
                        'event' => 'user.role_updated',
                        'timestamp' => now()->timestamp,
                        'data' => [
                            'email' => $user->email,
                            'name' => $user->name,
                            'access_status' => $currentStatus,
                            'role' => $currentRole,
                            'client_id' => (int) $client->id,
                        ],
                        'signature' => hash_hmac('sha256', $user->email.':'.$currentRole, $client->secret),
                    ]);
                } catch (\Exception $e) {
                    // silent webhook failure
                }
            }

            return back()->with('success', "Akses & role lokal '{$user->name}' untuk {$client->name} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui akses pengguna: '.$e->getMessage()]);
        }
    }
}
