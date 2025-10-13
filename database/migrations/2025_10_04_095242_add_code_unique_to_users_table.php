<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'code_unique')) {
                $table->string('code_unique', 20)->unique()->nullable()->after('email');
            }
        });

        // Générer des codes uniques pour tous les utilisateurs existants
        $users = \App\Models\User::whereNull('code_unique')->get();
        
        foreach ($users as $user) {
            // Générer un code selon le rôle
            $prefix = $this->getPrefixByRole($user);
            
            do {
                $code = $prefix . '-' . strtoupper(substr(uniqid(), -8));
            } while (\App\Models\User::where('code_unique', $code)->exists());
            
            $user->code_unique = $code;
            $user->save();
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code_unique');
        });
    }

    private function getPrefixByRole($user)
    {
        if ($user->hasRole('admin')) {
            return 'ADM';
        } elseif ($user->hasRole('proprietaire')) {
            return 'PROP';
        } elseif ($user->hasRole('locataire')) {
            return 'LOC';
        } elseif ($user->hasRole('gestionnaire')) {
            return 'GEST';
        } else {
            return 'USER';
        }
    }
};