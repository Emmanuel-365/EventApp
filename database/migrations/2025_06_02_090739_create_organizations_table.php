<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('matricule')->unique();
            $table->string('nom');
            $table->string('NIU');
            $table->string('type');
            $table->timestamp('date_creation')->nullable();
            $table->foreignIdFor(\App\Models\Organizer::class);
            $table->json('data')->nullable();

            // Statut de validation de l'organisation
            $table->string('validation_status')->default('pending')->after('date_creation');
            $table->text('rejected_reason')->nullable()->after('validation_status');

            // Statut d'activation de l'organisation
            $table->string('activation_status')->default('disabled')->after('rejected_reason');
            $table->text('disabled_reason')->nullable()->after('activation_status');
            $table->string('disabled_by_type')->nullable()->after('disabled_reason'); // 'admin' ou 'organizer'
            $table->uuid('disabled_by_id')->nullable()->after('disabled_by_type'); // ID de l'Admin ou Organizer


            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
