<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->date('pickup_date');
            $table->string('status')->default('pending'); // pending | ready | collected
            $table->timestamps();

            // Only one pickup record per subscription per day.
            $table->unique(['subscription_id', 'pickup_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_schedules');
    }
};
