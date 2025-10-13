<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etats_lieux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('contrat_id');
            $table->enum('type_etat', ['entree', 'sortie']);
            $table->date('date_etat');
            $table->json('details_equipements');
            $table->json('photos')->nullable();
            $table->text('observations')->nullable();
            $table->json('degats_constates')->nullable();
            $table->decimal('cout_reparations', 12, 2)->default(0);
            $table->timestamp('date_signature_locataire')->nullable();
            $table->timestamp('date_signature_proprietaire')->nullable();
            $table->text('document_url')->nullable();
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clé étrangère
            $table->foreign('contrat_id')
                  ->references('id')
                  ->on('contrats_location')
                  ->onDelete('cascade');

            // Index
            $table->index('contrat_id');
            $table->index('type_etat');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etats_lieux');
    }
};
