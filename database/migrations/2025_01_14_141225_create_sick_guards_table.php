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
        Schema::create('sick_guards', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('sexe');
            $table->date('dateNaissance');
            $table->string('lieuNaissance');
            $table->string('telephone');
            $table->string('pays')->nullable();
            $table->string('ville')->nullable();
            $table->string('quartier')->nullable();
            $table->string('photoProfil')->nullable();
            $table->string('pieceIdentite')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->softDeletes();

            $table->timestamps();
        });

        Schema::table('sick_guards', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Admin::class)->nullable();
            $table->boolean('active')->nullable()->default(null);
            $table->string('status')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sick_guards');
    }
};
