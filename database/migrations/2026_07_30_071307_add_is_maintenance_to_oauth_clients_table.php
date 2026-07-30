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
            $table->boolean('is_maintenance')->default(false)->after('supported_roles');
            $table->text('maintenance_message')->nullable()->after('is_maintenance');
            $table->string('description')->nullable()->after('maintenance_message');
            $table->integer('display_order')->default(0)->after('description');
            $table->boolean('is_visible')->default(true)->after('display_order');
            $table->string('logo_path')->nullable()->after('is_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn([
                'is_maintenance',
                'maintenance_message',
                'description',
                'display_order',
                'is_visible',
                'logo_path',
            ]);
        });
    }
};
