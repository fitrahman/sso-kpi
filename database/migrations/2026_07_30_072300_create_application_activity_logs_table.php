<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('oauth_client_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('action'); // maintenance_on, maintenance_off, name_updated, logo_updated, visibility_changed, etc.
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_activity_logs');
    }
};

