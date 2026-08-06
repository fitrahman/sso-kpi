<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('discovery_url')->nullable()->after('supported_roles');
            $table->string('discovery_secret')->nullable()->after('discovery_url');
            $table->json('supported_roles')->nullable()->change();
            $table->timestamp('roles_synced_at')->nullable()->after('discovery_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('supported_roles')->nullable()->change();
            $table->dropColumn(['discovery_url', 'discovery_secret', 'roles_synced_at']);
        });
    }
};
