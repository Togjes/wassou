<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consommation_avances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('avance_id');
            $table->uuid('paiement_id');
            $table->decimal('montant_consomme', 12, 2);
            $table->date('periode_couverte_debut')->nullable();
            $table->date('periode_couverte_fin')->nullable();
            $table->text('description')->nullable();
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clés étrangères
            $table->foreign('avance_id')
                  ->references('id')
                  ->on('avances_prepayees')
                  ->onDelete('cascade');
                  
            $table->foreign('paiement_id')
                  ->references('id')
                  ->on('paiements')
                  ->onDelete('cascade');

            // Index
            $table->index('avance_id');
            $table->index('paiement_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consommation_avances');
    }
};
