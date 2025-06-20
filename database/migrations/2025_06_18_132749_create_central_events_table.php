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
        Schema::create('central_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tenant_id');
            $table->string('event_id');

            $table->string('matricule')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('time');
            $table->string('location');
            $table->float('price');
            $table->integer('capacity');
            $table->integer('available_tickets');
            $table->string('status');
            $table->string('image_url')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->string('cancelled_by_type')->nullable();
            $table->uuid('cancelled_by_id')->nullable();

            $table->string('latitude')->nullable()->after('location');
            $table->string('longitude')->nullable()->after('latitude');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'event_id']);
            $table->index('status');
            $table->index('date');

            $table->foreign('tenant_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_events');
    }
};
