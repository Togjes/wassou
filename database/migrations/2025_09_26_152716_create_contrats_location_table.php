<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats_location', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('chambre_id');
            $table->uuid('proprietaire_id');
            $table->uuid('locataire_id');
            $table->uuid('demarcheur_id')->nullable();
            $table->string('numero_contrat', 50)->unique();
            
            // Dates
            $table->date('date_etablissement');
            $table->integer('date_paiement_loyer')->default(1);
            
            // Documents
            $table->timestamp('date_signature_proprietaire')->nullable();
            $table->timestamp('date_signature_locataire')->nullable();
            
            // Statut
            $table->enum('statut', ['brouillon', 'en_attente', 'actif', 'expire', 'resilie'])->default('brouillon');
            
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clés étrangères
            $table->foreign('chambre_id')
                  ->references('id')
                  ->on('chambres')
                  ->onDelete('cascade');

            $table->foreign('proprietaire_id')
                  ->references('id')
                  ->on('proprietaires')
                  ->onDelete('cascade');
                  
            $table->foreign('locataire_id')
                  ->references('id')
                  ->on('locataires')
                  ->onDelete('cascade');
                  
            $table->foreign('demarcheur_id')
                  ->references('id')
                  ->on('demarcheurs')
                  ->onDelete('set null');

            // Index
            $table->index('chambre_id');
            $table->index('proprietaire_id');
            $table->index('locataire_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats_location');
    }
};
