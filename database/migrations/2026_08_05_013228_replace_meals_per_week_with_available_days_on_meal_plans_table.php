<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_plans', function (Blueprint $table) {
            $table->dropColumn('meals_per_week');
            $table->json('available_days')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('meal_plans', function (Blueprint $table) {
            $table->dropColumn('available_days');
            $table->unsignedTinyInteger('meals_per_week')->default(5);
        });
    }
};
