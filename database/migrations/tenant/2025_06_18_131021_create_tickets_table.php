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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id');
            $table->string('attendee_name');
            $table->string('attendee_email');
            $table->float('price');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_refunded')->default(false);
            $table->string('payment_id')->nullable();
            $table->uuid('ticket_code')->unique();
            $table->timestamp('scanned_at')->nullable();
            $table->string('status')->default('valid');
            $table->uuid('client_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
