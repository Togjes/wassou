<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens_immobiliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('proprietaire_id');
            $table->text('titre');
            $table->text('description')->nullable();
            $table->enum('type_bien', ['maison', 'appartement', 'bureau', 'terrain', 'commerce', 'magasin', 'autre']);
            $table->string('ville', 100);
            $table->string('quartier', 100);
            $table->text('adresse')->nullable();
            $table->date('annee_construction')->nullable();
            $table->json('equipements_communs')->nullable();
            $table->json('photos_generales')->nullable();
            $table->json('documents')->nullable();
            $table->json('moyens_paiement_acceptes');
            $table->json('details_paiement')->nullable();
            $table->enum('statut', ['Construction', 'Renovation', 'Location'])->default('Location');
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clé étrangère
            $table->foreign('proprietaire_id')
                  ->references('id')
                  ->on('proprietaires')
                  ->onDelete('cascade');

            // Index
            $table->index('proprietaire_id');
            $table->index('ville');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens_immobiliers');
    }
};
