<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chambres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bien_id');
            $table->string('nom_chambre', 20);
            $table->text('description')->nullable();
            $table->decimal('surface_m2', 8, 2)->nullable();
            $table->integer('nombre_pieces')->default(1);
            $table->enum('type_chambre', ['chambre_simple', 'chambre_salon', 'magasin', 'bureau']);
            $table->json('equipements')->nullable();
            $table->json('photos')->nullable();
            $table->decimal('loyer_mensuel', 12, 2);
            $table->decimal('avance', 12, 2)->nullable();
            $table->decimal('prepaye', 12, 2)->nullable();
            $table->decimal('caution', 12, 2)->nullable();
            $table->boolean('disponible')->default(true);
            $table->enum('statut', ['disponible', 'loue', 'renovation', 'reserve'])->default('disponible');
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clé étrangère
            $table->foreign('bien_id')
                  ->references('id')
                  ->on('biens_immobiliers')
                  ->onDelete('cascade');

            // Contraintes
            $table->unique(['bien_id', 'nom_chambre']);

            // Index
            $table->index('bien_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chambres');
    }
};
