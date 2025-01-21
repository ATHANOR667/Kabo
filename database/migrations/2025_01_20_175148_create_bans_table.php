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
        Schema::create('bans', function (Blueprint $table) {
            $table->id();
            $table->boolean('isBanned')->default(true);
            $table->string('motif')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('bans', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Client::class)->nullable();
            $table->foreignIdFor(\App\Models\SickGuard::class)->nullable();
            $table->foreignIdFor(\App\Models\Admin::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bans');
    }
};
