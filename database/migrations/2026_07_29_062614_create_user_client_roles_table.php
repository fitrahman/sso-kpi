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
        Schema::create('user_client_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('oauth_client_id');
            $table->foreign('oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->string('role');
            $table->timestamps();

            // Unique constraint on (user_id, oauth_client_id)
            $table->unique(['user_id', 'oauth_client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_client_roles');
    }
};
