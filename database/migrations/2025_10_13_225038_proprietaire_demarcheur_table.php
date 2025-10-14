<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proprietaire_demarcheur', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proprietaire_id');
            $table->uuid('demarcheur_id');
            
            // Statut de la relation
            $table->enum('statut', ['actif', 'suspendu', 'refuse'])->default('actif');
            
            // Autorisations spécifiques (optionnel)
            $table->json('permissions')->nullable(); // Ex: ['creer_bien', 'modifier_bien', 'creer_contrat']
            
            // Qui a initié la demande
            $table->enum('demande_initiee_par', ['proprietaire', 'demarcheur'])->default('proprietaire');
            
            // Date de validation/acceptation
            $table->timestamp('date_validation')->nullable();
            
            // Notes ou commentaires
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Clés étrangères
            $table->foreign('proprietaire_id')
                  ->references('id')
                  ->on('proprietaires')
                  ->onDelete('cascade');

            $table->foreign('demarcheur_id')
                  ->references('id')
                  ->on('demarcheurs')
                  ->onDelete('cascade');

            // Contrainte d'unicité : un démarcheur ne peut être lié qu'une fois au même propriétaire
            $table->unique(['proprietaire_id', 'demarcheur_id'], 'unique_proprietaire_demarcheur');

            // Index pour les recherches
            $table->index('proprietaire_id');
            $table->index('demarcheur_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proprietaire_demarcheur');
    }
};