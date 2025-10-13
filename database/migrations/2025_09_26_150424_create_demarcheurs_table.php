<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demarcheurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('default_mobile_money_number', 20)->nullable();
            $table->json('bank_account_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Clé étrangère
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demarcheurs');
    }
};
