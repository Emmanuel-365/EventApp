<?php

// database/migrations/tenant/YYYY_MM_DD_create_events_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('matricule');


            $table->string('title');
            $table->text('description')->nullable();
            $table->string('date');
            $table->string('time');
            $table->string('location');
            $table->string('image_url')->nullable();

            $table->string('price');
            $table->string('capacity');
            $table->string('available_tickets');
            $table->string('status')->default('not_published');

            $table->string('cancelled_reason')->nullable()->after('image_url');
            $table->string('cancelled_by_type')->nullable()->after('cancelled_reason');
            $table->uuid('cancelled_by_id')->nullable()->after('cancelled_by_type');
            $table->index(['cancelled_by_id']);

            $table->string('latitude')->nullable()->after('location');
            $table->string('longitude')->nullable()->after('latitude');



            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
