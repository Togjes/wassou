<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->uuid('created_by_user_id')->nullable()->after('proprietaire_id');
            $table->string('created_by_type', 50)->nullable()->after('created_by_user_id'); // 'admin', 'proprietaire', 'demarcheur'
            
            $table->foreign('created_by_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->index('created_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn(['created_by_user_id', 'created_by_type']);
        });
    }
};