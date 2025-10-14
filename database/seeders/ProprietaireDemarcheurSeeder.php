<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Proprietaire;
use App\Models\Demarcheur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ProprietaireDemarcheurSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // Tableau de propriétaires à créer
            $proprietaires = [
                [
                    'email' => 'proprietaire1@test.com',
                    'first_name' => 'Jean',
                    'last_name' => 'Dupont',
                    'mobile_money' => '+22997000001',
                ],
                [
                    'email' => 'proprietaire2@test.com',
                    'first_name' => 'Pierre',
                    'last_name' => 'Martin',
                    'mobile_money' => '+22997000002',
                ],
            ];

            // Tableau de démarcheurs à créer
            $demarcheurs = [
                [
                    'email' => 'demarcheur1@test.com',
                    'first_name' => 'Marie',
                    'last_name' => 'Kouassi',
                ],
                [
                    'email' => 'demarcheur2@test.com',
                    'first_name' => 'Sophie',
                    'last_name' => 'Diallo',
                ],
            ];

            $proprietairesCreated = [];
            $demarcheursCreated = [];

            // Créer les propriétaires
            foreach ($proprietaires as $propData) {
                $user = User::firstOrCreate(
                    ['email' => $propData['email']],
                    [
                        'password_hash' => Hash::make('password'),
                        'first_name' => $propData['first_name'],
                        'last_name' => $propData['last_name'],
                        'user_type' => 'proprietaire',
                        'ville' => 'Cotonou',
                        'pays' => 'Bénin',
                        'is_active' => true,
                        'is_verified' => true,
                    ]
                );

                if (!$user->hasRole('proprietaire')) {
                    $user->assignRole('proprietaire');
                }

                $proprietaire = Proprietaire::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'ville' => 'Cotonou',
                        'pays' => 'Bénin',
                        'mobile_money_number' => $propData['mobile_money'],
                    ]
                );

                $proprietairesCreated[] = [
                    'user' => $user,
                    'proprietaire' => $proprietaire,
                ];

                $this->command->info("✓ Propriétaire créé: {$user->name} (Code: {$user->code_unique})");
            }

            // Créer les démarcheurs
            foreach ($demarcheurs as $demData) {
                $user = User::firstOrCreate(
                    ['email' => $demData['email']],
                    [
                        'password_hash' => Hash::make('password'),
                        'first_name' => $demData['first_name'],
                        'last_name' => $demData['last_name'],
                        'user_type' => 'demarcheur',
                        'ville' => 'Cotonou',
                        'pays' => 'Bénin',
                        'is_active' => true,
                        'is_verified' => true,
                    ]
                );

                if (!$user->hasRole('demarcheur')) {
                    $user->assignRole('demarcheur');
                }

                $demarcheur = Demarcheur::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'is_active' => true,
                    ]
                );

                $demarcheursCreated[] = [
                    'user' => $user,
                    'demarcheur' => $demarcheur,
                ];

                $this->command->info("✓ Démarcheur créé: {$user->name} (Code: {$user->code_unique})");
            }

            // Créer les relations
            // Propriétaire 1 -> Démarcheur 1 (toutes permissions)
            if (!$proprietairesCreated[0]['proprietaire']->hasDemarcheur($demarcheursCreated[0]['demarcheur']->id)) {
                $proprietairesCreated[0]['proprietaire']->ajouterDemarcheur(
                    $demarcheursCreated[0]['demarcheur']->id,
                    [
                        'permissions' => [], // Toutes permissions
                        'notes' => 'Gestionnaire principal',
                    ]
                );
                $this->command->info("✓ Relation créée: Propriétaire 1 <-> Démarcheur 1 (toutes permissions)");
            }

            // Propriétaire 1 -> Démarcheur 2 (permissions limitées)
            if (!$proprietairesCreated[0]['proprietaire']->hasDemarcheur($demarcheursCreated[1]['demarcheur']->id)) {
                $proprietairesCreated[0]['proprietaire']->ajouterDemarcheur(
                    $demarcheursCreated[1]['demarcheur']->id,
                    [
                        'permissions' => ['creer_bien', 'creer_chambre'],
                        'notes' => 'Gestionnaire secondaire',
                    ]
                );
                $this->command->info("✓ Relation créée: Propriétaire 1 <-> Démarcheur 2 (permissions limitées)");
            }

            // Propriétaire 2 -> Démarcheur 1 (permissions spécifiques)
            if (!$proprietairesCreated[1]['proprietaire']->hasDemarcheur($demarcheursCreated[0]['demarcheur']->id)) {
                $proprietairesCreated[1]['proprietaire']->ajouterDemarcheur(
                    $demarcheursCreated[0]['demarcheur']->id,
                    [
                        'permissions' => ['creer_bien', 'modifier_bien', 'creer_chambre', 'modifier_chambre'],
                        'notes' => 'Gestionnaire avec permissions étendues',
                    ]
                );
                $this->command->info("✓ Relation créée: Propriétaire 2 <-> Démarcheur 1 (permissions étendues)");
            }

            DB::commit();

            // Afficher le récapitulatif
            $this->command->info("\n" . str_repeat("=", 60));
            $this->command->info("RÉCAPITULATIF DES COMPTES CRÉÉS");
            $this->command->info(str_repeat("=", 60));
            
            $this->command->info("\nPROPRIÉTAIRES:");
            foreach ($proprietairesCreated as $index => $prop) {
                $this->command->info("  " . ($index + 1) . ". {$prop['user']->name}");
                $this->command->info("     Email: {$prop['user']->email}");
                $this->command->info("     Code: {$prop['user']->code_unique}");
                $this->command->info("     Password: password\n");
            }

            $this->command->info("DÉMARCHEURS:");
            foreach ($demarcheursCreated as $index => $dem) {
                $this->command->info("  " . ($index + 1) . ". {$dem['user']->name}");
                $this->command->info("     Email: {$dem['user']->email}");
                $this->command->info("     Code: {$dem['user']->code_unique}");
                $this->command->info("     Password: password\n");
            }

            $this->command->info(str_repeat("=", 60) . "\n");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Erreur lors du seeding: " . $e->getMessage());
            throw $e;
        }
    }
}