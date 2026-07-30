<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('supported_roles')->nullable()->after('redirect');
        });

        // Update existing client roles data
        DB::table('oauth_clients')->where('id', 2)->update(['supported_roles' => json_encode(['admin', 'atasan', 'pegawai'])]);
        DB::table('oauth_clients')->where('id', 3)->update(['supported_roles' => json_encode(['admin', 'pengguna'])]);
        DB::table('oauth_clients')->where('id', 4)->update(['supported_roles' => json_encode(['admin', 'edit', 'view'])]);
        DB::table('oauth_clients')->where('id', 5)->update(['supported_roles' => json_encode(['admin', 'pengguna'])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('supported_roles');
        });
    }
};
