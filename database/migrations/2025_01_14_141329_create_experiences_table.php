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
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('nomEntreprise');
            $table->string('typeEntreprise');
            $table->string('poste');
            $table->string('description');
            $table->string('dateDebut');
            $table->string('dateFin');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('experiences', function(Blueprint $table){
            $table->foreignIdFor(\App\Models\SickGuard::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
