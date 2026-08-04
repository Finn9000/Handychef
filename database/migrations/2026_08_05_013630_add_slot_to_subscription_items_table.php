<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropUnique(['subscription_id', 'meal_item_id']);
            $table->string('slot')->nullable()->after('meal_item_id');
            $table->unique(['subscription_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::table('subscription_items', function (Blueprint $table) {
            $table->dropUnique(['subscription_id', 'slot']);
            $table->dropColumn('slot');
            $table->unique(['subscription_id', 'meal_item_id']);
        });
    }
};
