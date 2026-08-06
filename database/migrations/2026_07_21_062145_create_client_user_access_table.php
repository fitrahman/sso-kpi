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
        Schema::create('client_user_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Passport oauth_clients id could be char(36) or bigInteger depending on config.
            // In modern laravel with Passport, it's uuid.
            // We won't strictly constrain it at DB level to avoid issues if it's different.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_user_access');
    }
};
