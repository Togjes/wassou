<?php

namespace Database\Seeders;

use App\Models\BienImmobilier;
use Illuminate\Database\Seeder;

class UpdateExistingBiensCreatorSeeder extends Seeder
{
    public function run(): void
    {
        // Pour tous les biens sans créateur, on assigne le propriétaire comme créateur
        $biens = BienImmobilier::whereNull('created_by_user_id')->get();

        foreach ($biens as $bien) {
            $bien->update([
                'created_by_user_id' => $bien->proprietaire->user_id,
                'created_by_type' => 'proprietaire',
            ]);
        }

        $this->command->info("✓ {$biens->count()} bien(s) mis à jour avec le créateur");
    }
}