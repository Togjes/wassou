<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chambres', function (Blueprint $table) {
            $table->string('reference', 20)->unique()->nullable()->after('nom_chambre');
        });

        // Générer des références pour les chambres existantes
        $chambres = \App\Models\Chambre::whereNull('reference')->get();
        foreach ($chambres as $chambre) {
            $chambre->reference = \App\Models\Chambre::generateReference($chambre->bien_id);
            $chambre->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('chambres', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};