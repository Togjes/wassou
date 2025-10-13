<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Wassou',
            'email' => 'admin@wassou.com',
            'password_hash' => 'password',
            'user_type' => 'admin',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Propriétaire
        $proprietaire = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'proprietaire@wassou.com',
            'password_hash' => 'password',
            'user_type' => 'proprietaire',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $proprietaire->creerProfilSpecifique([
            'profession' => 'Entrepreneur',
            'mobile_money_number' => '+229 12345678',
        ]);

        // Locataire
        $locataire = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Martin',
            'email' => 'locataire@wassou.com',
            'password_hash' => 'password',
            'user_type' => 'locataire',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Ne pas passer ville/pays pour locataire
        $locataire->creerProfilSpecifique([
            'profession' => 'Enseignante',
            'mobile_money_number' => '+229 87654321',
            'adresse_actuelle' => 'Quartier Akpakpa, Cotonou',
        ]);

        // Démarcheur
        $demarcheur = User::create([
            'first_name' => 'Paul',
            'last_name' => 'Kouassi',
            'email' => 'demarcheur@wassou.com',
            'password_hash' => 'password',
            'user_type' => 'demarcheur',
            'ville' => 'Cotonou',
            'pays' => 'Bénin',
            'is_active' => true,
            'is_verified' => true,
        ]);

        $demarcheur->creerProfilSpecifique([
            'default_mobile_money_number' => '+229 98765432',
        ]);

        $this->command->info('✅ Utilisateurs de test créés avec succès !');
        $this->command->info('📧 Admin: admin@wassou.com');
        $this->command->info('📧 Propriétaire: proprietaire@wassou.com');
        $this->command->info('📧 Locataire: locataire@wassou.com');
        $this->command->info('📧 Démarcheur: demarcheur@wassou.com');
        $this->command->info('🔑 Mot de passe pour tous: password');
    }
}