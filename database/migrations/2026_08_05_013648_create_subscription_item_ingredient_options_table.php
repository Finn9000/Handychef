<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_item_ingredient_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_item_ingredient_option_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_item_ingredient_options');
    }
};
