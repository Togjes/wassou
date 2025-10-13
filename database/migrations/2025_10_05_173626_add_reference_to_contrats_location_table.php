<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->string('reference', 30)->unique()->nullable()->after('numero_contrat');
        });

        // Générer des références pour les contrats existants
        $contrats = \App\Models\ContratLocation::whereNull('reference')->get();
        foreach ($contrats as $contrat) {
            $contrat->reference = \App\Models\ContratLocation::generateReference($contrat->chambre_id);
            $contrat->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('contrats_location', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};