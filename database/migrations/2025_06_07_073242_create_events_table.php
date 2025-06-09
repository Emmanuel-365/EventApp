<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->uuid('matricule')->unique();
            $table->string('nom');
            $table->string('image');
            $table->string('video')->nullable();
            $table->text('description');
            $table->timestamp('dateDebut');
            $table->timestamp('dateFin');
            $table->string('location');
            $table->integer('prix');
            $table->uuid('organization_id');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
