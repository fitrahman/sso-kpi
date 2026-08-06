<?php

namespace App\Http\Middleware;

use App\Models\PassportClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOAuthAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Hanya proses rute /oauth/authorize (menggunakan GET karena redirect dari klien)
        if ($request->is('oauth/authorize') && $request->isMethod('get') && Auth::check()) {
            $clientId = $request->query('client_id');

            if ($clientId) {
                $user = Auth::user();
                $client = PassportClient::find($clientId);

                if ($client) {
                    // Blokir jika aplikasi sedang maintenance (kecuali admin)
                    if ($client->is_maintenance && $user->role !== 'admin') {
                        return redirect()->route('app.maintenance', [
                            'appName' => $client->name,
                            'message' => $client->maintenance_message,
                        ]);
                    }

                    if ($user->role === 'admin') {
                        return $next($request);
                    }

                    $access = $user->accessedClients()->where('client_id', $clientId)->first();

                    if (! $access || $access->pivot->status !== 'approved') {
                        // Arahkan ke gateway agar menampilkan halaman request akses / pending
                        return redirect()->route('app.gateway', ['appName' => $client->name]);
                    }
                }
            }
        }

        return $next($request);
    }
}
