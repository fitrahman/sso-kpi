<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Data Migration: fill NULL/empty role values with 'user'
        DB::table('user_client_roles')
            ->whereNull('role')
            ->orWhere('role', '')
            ->update(['role' => 'user']);

        // 2. Schema Change: Make role column non-nullable with 'user' as default
        Schema::table('user_client_roles', function (Blueprint $table) {
            $table->string('role')->default('user')->nullable(false)->change();
        });

        // 3. Schema Change: Add is_active column to client_user_access table
        Schema::table('client_user_access', function (Blueprint $table) {
            $table->boolean('is_active')->default(false)->after('status');
        });

        // 4. Data Migration: set is_active to true where access status was 'approved'
        DB::table('client_user_access')
            ->where('status', 'approved')
            ->update(['is_active' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_user_access', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('user_client_roles', function (Blueprint $table) {
            $table->string('role')->nullable()->default(null)->change();
        });
    }
};
