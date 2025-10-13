<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email', 100)->unique();
            $table->string('phone', 20)->unique()->nullable();
            $table->text('password_hash');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_naissance')->nullable();
            $table->enum('user_type', ['admin', 'proprietaire', 'locataire', 'demarcheur']);
            $table->string('ville', 100);
            $table->string('pays', 100);
            $table->text('profile_image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->date('email_verified_at')->nullable();
            $table->date('phone_verified_at')->nullable();
            $table->date('created_at');
            $table->date('updated_at')->nullable();
            $table->date('deleted_at')->nullable();

            // Index
            $table->index('email');
            $table->index('phone');
            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
