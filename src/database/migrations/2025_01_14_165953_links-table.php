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
        if (!Schema::hasTable('links')) {
            Schema::create('links', function (Blueprint $table) {
                $table->id();
                $table->string('user');
                $table->string('long_url', 2048);
                $table->unsignedInteger('hits')->default(0);
                $table->timestamps();
                $table->index(['user', 'long_url (key(250))'], 'user_long_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
