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
        Schema::create('organization_organizer', function (Blueprint $table) {

            $table->increments('id');
            $table->string('organization_id');
            $table->string('organizer_id');

            /** $table->unique(['organization_id', 'organizer_id']); */

            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('organizer_id')->references('global_id')->on('organizers')->onUpdate('cascade')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_organizer');
    }


};
