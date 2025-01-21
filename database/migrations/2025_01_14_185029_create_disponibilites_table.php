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
        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('debut');
            $table->string('fin');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('disponibilites', function(Blueprint $table){
            $table->foreignIdFor(\App\Models\SickGuard::class);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilites');
    }
};
