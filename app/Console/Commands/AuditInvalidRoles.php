<?php

namespace App\Console\Commands;

use App\Models\PassportClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditInvalidRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:invalid-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit user_client_roles for mismatch with supported_roles of client applications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Pre-load all clients to avoid querying client database for every row
        $clients = PassportClient::all()->keyBy('id');
        $clientRoles = [];

        foreach ($clients as $client) {
            $roles = [];
            if (! empty($client->supported_roles)) {
                $roles = json_decode($client->supported_roles, true);
            }
            if (! is_array($roles)) {
                $roles = [];
            }
            $clientRoles[$client->id] = array_map('strtolower', $roles);
        }

        $totalChecked = 0;
        $totalInvalid = 0;
        $invalidPerApp = [];
        $csvData = [];

        // 2. Loop user_client_roles using chunking
        DB::table('user_client_roles')->orderBy('id')->chunk(500, function ($rows) use ($clients, $clientRoles, &$totalChecked, &$totalInvalid, &$invalidPerApp, &$csvData) {
            $userIds = $rows->pluck('user_id')->unique();
            $users = DB::table('users')->whereIn('id', $userIds)->pluck('email', 'id');

            foreach ($rows as $row) {
                $totalChecked++;
                $client = $clients->get($row->oauth_client_id);
                $clientName = $client ? $client->name : 'Unknown App ('.$row->oauth_client_id.')';

                $supported = $clientRoles[$row->oauth_client_id] ?? [];
                $roleVal = strtolower(trim($row->role));

                if (! in_array($roleVal, $supported)) {
                    $totalInvalid++;
                    $invalidPerApp[$clientName] = ($invalidPerApp[$clientName] ?? 0) + 1;

                    $userEmail = $users->get($row->user_id) ?? 'Unknown Email';
                    $csvData[] = [
                        $row->user_id,
                        $userEmail,
                        $clientName,
                        $row->role,
                    ];
                }
            }
        });

        // 3. Display summary
        $this->info('=== HASIL AUDIT ROLE TIDAK COCOK ===');
        $this->info('Total baris dicek: '.$totalChecked);
        $this->info('Total baris invalid: '.$totalInvalid);

        if ($totalInvalid > 0) {
            $this->warn("\nBreakdown per aplikasi klien:");
            foreach ($invalidPerApp as $appName => $count) {
                $this->line("- {$appName}: {$count} baris invalid");
            }

            // 4. Export CSV
            $timestamp = time();
            $fileName = "audit-invalid-roles-{$timestamp}.csv";
            $filePath = storage_path("app/{$fileName}");

            if (! file_exists(storage_path('app'))) {
                mkdir(storage_path('app'), 0755, true);
            }

            $file = fopen($filePath, 'w');
            fputcsv($file, ['user_id', 'email', 'client', 'role_lama']);
            foreach ($csvData as $line) {
                fputcsv($file, $line);
            }
            fclose($file);

            $this->info("\nDetail lengkap diexport ke: storage/app/{$fileName}");
        } else {
            $this->info("\nSemua role cocok dengan supported_roles!");
        }

        return 0;
    }
}
