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
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
            $table->index('role');
        });

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->index('activity');
            $table->index('created_at');
        });

        Schema::table('client_user_access', function (Blueprint $table) {
            $table->index(['client_id', 'is_active']);
            $table->index(['user_id', 'client_id']);
        });

        Schema::table('application_activity_logs', function (Blueprint $table) {
            $table->index('oauth_client_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_status_index']);
            $table->dropIndex(['users_role_index']);
        });

        Schema::table('user_activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_activity_logs_activity_index']);
            $table->dropIndex(['user_activity_logs_created_at_index']);
        });

        Schema::table('client_user_access', function (Blueprint $table) {
            $table->dropIndex(['client_user_access_client_id_is_active_index']);
            $table->dropIndex(['client_user_access_user_id_client_id_index']);
        });

        Schema::table('application_activity_logs', function (Blueprint $table) {
            $table->dropIndex(['application_activity_logs_oauth_client_id_index']);
            $table->dropIndex(['application_activity_logs_created_at_index']);
        });
    }
};
