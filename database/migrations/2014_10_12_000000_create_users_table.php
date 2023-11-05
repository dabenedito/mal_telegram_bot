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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('chat_id')->unique()->primary();
            $table->string('first_name');
            $table->string('last_name')->nullable()->default(null);
            $table->string('username')->unique()->nullable()->default(null);
            $table->text('access_token')->nullable()->default(null);
            $table->text('refresh_token')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
