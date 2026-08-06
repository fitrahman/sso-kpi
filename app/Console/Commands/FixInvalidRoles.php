<?php

namespace App\Console\Commands;

use App\Models\PassportClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FixInvalidRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invalid-roles {--apply : Execute the actual updates in the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invalid user_client_roles using the lowest level supported_role for the client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apply = $this->option('apply');

        $this->info($apply ? '=== MEMULAI PERBAIKAN ROLE (REAL MODE) ===' : '=== MEMULAI AUDIT & PREVIEW PERBAIKAN ROLE (DRY-RUN) ===');

        // 1. Pre-load clients
        $clients = PassportClient::all()->keyBy('id');
        $clientLowestRole = [];

        // 2. Discover lowest role for each client
        foreach ($clients as $client) {
            $lowestRole = null;

            if (! empty($client->discovery_url) && ! empty($client->discovery_secret)) {
                try {
                    $res = Http::withHeaders([
                        'X-SSO-Secret' => $client->discovery_secret,
                    ])->timeout(3)->get($client->discovery_url);

                    if ($res->successful()) {
                        $json = $res->json();
                        if (isset($json['roles']) && is_array($json['roles'])) {
                            // Sort by level ascending
                            usort($json['roles'], function ($a, $b) {
                                return ($a['level'] ?? 0) <=> ($b['level'] ?? 0);
                            });
                            $lowestRole = $json['roles'][0]['key'] ?? null;
                        }
                    }
                } catch (\Exception $e) {
                    // silent fallback
                }
            }

            if (! $lowestRole) {
                // Fallback to supported_roles table column
                $roles = [];
                if (! empty($client->supported_roles)) {
                    $roles = json_decode($client->supported_roles, true);
                }
                if (! is_array($roles)) {
                    $roles = [];
                }

                if (! empty($roles)) {
                    // Find a non-admin role first
                    $nonAdmin = array_filter($roles, function ($r) {
                        return ! in_array(strtolower($r), ['admin', 'superadmin']);
                    });
                    if (! empty($nonAdmin)) {
                        $lowestRole = reset($nonAdmin);
                    } else {
                        $lowestRole = $roles[0];
                    }
                } else {
                    $lowestRole = 'user';
                }
            }

            $clientLowestRole[$client->id] = $lowestRole;
            $this->line("App '{$client->name}' lowest role fallback: '{$lowestRole}'");
        }

        $this->line('');

        $totalChecked = 0;
        $totalFixed = 0;
        $logData = [];

        // 3. Process records in chunks
        DB::table('user_client_roles')->orderBy('id')->chunk(500, function ($rows) use ($clients, $clientLowestRole, $apply, &$totalChecked, &$totalFixed, &$logData) {
            $userIds = $rows->pluck('user_id')->unique();
            $users = DB::table('users')->whereIn('id', $userIds)->pluck('email', 'id');

            $batchUpdates = [];

            foreach ($rows as $row) {
                $totalChecked++;
                $client = $clients->get($row->oauth_client_id);
                $clientName = $client ? $client->name : 'Unknown App ('.$row->oauth_client_id.')';

                $supported = [];
                if ($client && ! empty($client->supported_roles)) {
                    $supported = json_decode($client->supported_roles, true);
                }
                if (! is_array($supported)) {
                    $supported = [];
                }
                $supportedLower = array_map('strtolower', $supported);

                $roleVal = strtolower(trim($row->role));

                if (! in_array($roleVal, $supportedLower)) {
                    $newRole = $clientLowestRole[$row->oauth_client_id] ?? 'user';
                    $userEmail = $users->get($row->user_id) ?? 'Unknown Email';

                    $batchUpdates[] = [
                        'id' => $row->id,
                        'user_id' => $row->user_id,
                        'email' => $userEmail,
                        'client' => $clientName,
                        'role_lama' => $row->role,
                        'role_baru' => $newRole,
                    ];
                }
            }

            if (! empty($batchUpdates)) {
                if ($apply) {
                    // Update in transaction block
                    DB::transaction(function () use ($batchUpdates) {
                        foreach ($batchUpdates as $update) {
                            DB::table('user_client_roles')
                                ->where('id', $update['id'])
                                ->update([
                                    'role' => $update['role_baru'],
                                    'updated_at' => now(),
                                ]);
                        }
                    });
                    $totalFixed += count($batchUpdates);
                    // Sleep/usleep to ease DB load
                    usleep(50000); // 50 milliseconds delay
                } else {
                    $totalFixed += count($batchUpdates);
                }

                foreach ($batchUpdates as $update) {
                    $logData[] = [
                        'timestamp' => now()->toDateTimeString(),
                        'user_id' => $update['user_id'],
                        'email' => $update['email'],
                        'client' => $update['client'],
                        'role_lama' => $update['role_lama'],
                        'role_baru' => $update['role_baru'],
                    ];
                }

                // If dry-run, output changes preview to screen
                if (! $apply) {
                    foreach ($batchUpdates as $update) {
                        $this->line("[DRY-RUN] ID {$update['id']} | User: {$update['email']} | App: {$update['client']} | {$update['role_lama']} -> {$update['role_baru']}");
                    }
                }
            }
        });

        // 4. Summarize and log
        $this->info("\n=== HASIL EKSEKUSI ===");
        $this->info('Total baris dicek: '.$totalChecked);
        $this->info('Total baris yang '.($apply ? 'diperbaiki' : 'akan diperbaiki').': '.$totalFixed);

        if ($apply && ! empty($logData)) {
            $timestamp = time();
            $logFile = storage_path("logs/role-fix-{$timestamp}.log");

            if (! file_exists(storage_path('logs'))) {
                mkdir(storage_path('logs'), 0755, true);
            }

            $file = fopen($logFile, 'w');
            fwrite($file, "timestamp,user_id,email,client,role_lama,role_baru\n");
            foreach ($logData as $data) {
                fputcsv($file, array_values($data));
            }
            fclose($file);

            $this->info("\nDetail perbaikan ditulis ke: {$logFile}");
        }

        return 0;
    }
}
