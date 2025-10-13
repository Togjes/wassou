<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Création des permissions par module

        // ============= UTILISATEURS =============
        $userPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.activate',
            'users.deactivate',
        ];

        // ============= BIENS IMMOBILIERS =============
        $bienPermissions = [
            'biens.view',
            'biens.view-own',
            'biens.create',
            'biens.edit',
            'biens.edit-own',
            'biens.delete',
            'biens.delete-own',
        ];

        // ============= CHAMBRES =============
        $chambrePermissions = [
            'chambres.view',
            'chambres.view-own',
            'chambres.create',
            'chambres.edit',
            'chambres.edit-own',
            'chambres.delete',
            'chambres.delete-own',
        ];

        // ============= CONTRATS =============
        $contratPermissions = [
            'contrats.view',
            'contrats.view-own',
            'contrats.create',
            'contrats.edit',
            'contrats.edit-own',
            'contrats.delete',
            'contrats.sign',
            'contrats.cancel',
        ];

        // ============= PAIEMENTS =============
        $paiementPermissions = [
            'paiements.view',
            'paiements.view-own',
            'paiements.create',
            'paiements.edit',
            'paiements.validate',
            'paiements.cancel',
            'paiements.generate-receipt',
        ];

        // ============= AVANCES =============
        $avancePermissions = [
            'avances.view',
            'avances.view-own',
            'avances.create',
            'avances.edit',
            'avances.consume',
        ];

        // ============= ÉTATS DES LIEUX =============
        $etatLieuxPermissions = [
            'etats-lieux.view',
            'etats-lieux.view-own',
            'etats-lieux.create',
            'etats-lieux.edit',
            'etats-lieux.sign',
        ];

        // ============= NOTIFICATIONS =============
        $notificationPermissions = [
            'notifications.view',
            'notifications.view-own',
            'notifications.create',
            'notifications.delete',
        ];

        // ============= RAPPORTS =============
        $reportPermissions = [
            'reports.view',
            'reports.financial',
            'reports.occupancy',
            'reports.export',
        ];

        // Regrouper toutes les permissions
        $allPermissions = array_merge(
            $userPermissions,
            $bienPermissions,
            $chambrePermissions,
            $contratPermissions,
            $paiementPermissions,
            $avancePermissions,
            $etatLieuxPermissions,
            $notificationPermissions,
            $reportPermissions
        );

        // Créer toutes les permissions
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ============= CRÉATION DES RÔLES =============

        // ADMIN - Tous les droits
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // PROPRIÉTAIRE
        $proprietaireRole = Role::create(['name' => 'proprietaire']);
        $proprietaireRole->givePermissionTo([
            // Biens
            'biens.view-own',
            'biens.create',
            'biens.edit-own',
            'biens.delete-own',
            
            // Chambres
            'chambres.view-own',
            'chambres.create',
            'chambres.edit-own',
            'chambres.delete-own',
            
            // Contrats
            'contrats.view-own',
            'contrats.create',
            'contrats.edit-own',
            'contrats.sign',
            'contrats.cancel',
            
            // Paiements
            'paiements.view-own',
            'paiements.validate',
            'paiements.generate-receipt',
            
            // Avances
            'avances.view-own',
            'avances.create',
            'avances.consume',
            
            // États des lieux
            'etats-lieux.view-own',
            'etats-lieux.create',
            'etats-lieux.sign',
            
            // Notifications
            'notifications.view-own',
            
            // Rapports
            'reports.view',
            'reports.financial',
            'reports.occupancy',
            'reports.export',
        ]);

        // LOCATAIRE
        $locataireRole = Role::create(['name' => 'locataire']);
        $locataireRole->givePermissionTo([
            // Biens (consultation uniquement)
            'biens.view',
            
            // Chambres (consultation)
            'chambres.view',
            
            // Contrats
            'contrats.view-own',
            'contrats.sign',
            
            // Paiements
            'paiements.view-own',
            'paiements.create',
            
            // Avances
            'avances.view-own',
            
            // États des lieux
            'etats-lieux.view-own',
            'etats-lieux.sign',
            
            // Notifications
            'notifications.view-own',
        ]);

        // DÉMARCHEUR
        $demarcheurRole = Role::create(['name' => 'demarcheur']);
        $demarcheurRole->givePermissionTo([
            // Biens
            'biens.view',
            
            // Chambres
            'chambres.view',
            
            // Contrats (peut créer des contrats)
            'contrats.view',
            'contrats.create',
            
            // Paiements (consultation de ses commissions)
            'paiements.view-own',
            
            // Notifications
            'notifications.view-own',
        ]);

        $this->command->info('✅ Rôles et permissions créés avec succès !');
        $this->command->info('📊 Total permissions : ' . Permission::count());
        $this->command->info('👥 Total rôles : ' . Role::count());
    }
}