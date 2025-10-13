<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->string('reference', 20)->unique()->nullable()->after('proprietaire_id');
        });

        // Générer des références pour les biens existants
        $biens = \App\Models\BienImmobilier::whereNull('reference')->get();
        foreach ($biens as $bien) {
            $bien->reference = \App\Models\BienImmobilier::generateReference($bien->proprietaire_id);
            $bien->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};