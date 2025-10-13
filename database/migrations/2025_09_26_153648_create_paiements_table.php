<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contrat_id');
            $table->uuid('locataire_id');
            
            // Référence facture/reçu
            $table->string('numero_facture', 50)->unique();
            $table->string('numero_recu', 50)->unique()->nullable();
            
            // Type et détails
            $table->enum('type_paiement', ['loyer', 'charges', 'caution', 'frais_dossier', 'avance_loyer', 'prepaye_loyer', 'reparation', 'commission', 'penalite']);
            $table->decimal('montant', 12, 2);
            
            // Période
            $table->date('periode_debut')->nullable();
            $table->date('periode_fin')->nullable();
            $table->string('mois_concerne', 7)->nullable();
            
            // Dates
            $table->date('date_echeance');
            $table->timestamp('date_paiement')->nullable();
            
            // Moyen de paiement
            $table->enum('methode_paiement', ['mobile_money', 'carte_bancaire', 'virement', 'especes', 'cheque', 'depot_tranches']);
            $table->json('details_paiement')->nullable();
            $table->string('reference_transaction', 100)->nullable();
            $table->decimal('frais_transaction', 8, 2)->default(0);
            
            // Avances
            $table->boolean('utilise_avance')->default(false);
            $table->uuid('avance_utilisee_id')->nullable();
            $table->decimal('montant_avance_utilise', 12, 2)->default(0);
            
            // Statut et documents
            $table->enum('statut', ['en_attente', 'paye', 'en_retard', 'annule', 'rembourse'])->default('en_attente');
            $table->string('recu_url', 500)->nullable();
            $table->boolean('recu_pdf_genere')->default(false);
            
            // Traçabilité
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
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
                  
            $table->foreign('avance_utilisee_id')
                  ->references('id')
                  ->on('avances_prepayees')
                  ->onDelete('set null');

            // Index
            $table->index('contrat_id');
            $table->index('statut');
            $table->index('date_echeance');
            $table->index('type_paiement');
            $table->index('mois_concerne');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
