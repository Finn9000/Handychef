<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_item_ingredient_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meal_item_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type'); // add | remove
            $table->decimal('price_delta', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_item_ingredient_options');
    }
};
