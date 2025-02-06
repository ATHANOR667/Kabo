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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('annee');
            $table->string('mention');
            $table->string('institutionReference');
            $table->string('fichier')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('qualifications', function(Blueprint $table){
            $table->foreignIdFor(\App\Models\SickGuard::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
