<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avances_prepayees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contrat_id');
            $table->uuid('locataire_id');
            $table->enum('type_avance', ['avance_loyer', 'prepaye_loyer', 'avance_charges', 'prepaye_charges']);
            $table->decimal('montant_initial', 12, 2);
            $table->decimal('montant_restant', 12, 2);
            $table->decimal('montant_consomme', 12, 2)->default(0);
            $table->integer('nb_mois_couverts')->nullable();
            $table->date('date_debut_utilisation')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->enum('statut', ['actif', 'consomme', 'expire', 'rembourse'])->default('actif');
            $table->date('date_epuisement')->nullable();
            // Note: paiement_initial_id sera ajouté dans une migration séparée
            $table->text('notes')->nullable();
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clés étrangères
            $table->foreign('contrat_id')
                  ->references('id')
                  ->on('contrats_location')
                  ->onDelete('cascade');
                  
            $table->foreign('locataire_id')
                  ->references('id')
                  ->on('locataires')
                  ->onDelete('cascade');

            // Index
            $table->index('contrat_id');
            $table->index('statut');
            $table->index('locataire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avances_prepayees');
    }
};
