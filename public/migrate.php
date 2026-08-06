<?php

if (function_exists('opcache_reset')) {
    opcache_reset();
}
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    // Pastikan session driver adalah file saat proses refresh agar tidak error mencari tabel sessions
    config(['session.driver' => 'file']);

    Illuminate\Support\Facades\Artisan::call('config:clear');
    Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
        '--seed' => true,
        '--force' => true,
    ]);

    echo '<h1>Database sso_kpi Berhasil Di-fresh & Di-seed!</h1>';
    echo '<pre>'.Illuminate\Support\Facades\Artisan::output().'</pre>';
} catch (\Exception $e) {
    echo '<h1>Terjadi Error:</h1>';
    echo '<pre>'.$e->getMessage().'</pre>';
}
