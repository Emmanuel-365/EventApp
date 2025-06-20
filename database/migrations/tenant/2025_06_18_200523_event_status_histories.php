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
        Schema::create('event_status_histories', function (Blueprint $table) {
            $table->id();
            $table->string('event_id');
            $table->string('status_type');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('reason');

            $table->string('changed_by_type');
            $table->string('changed_by_id');

            $table->timestamps();
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_status_histories');
    }
};
