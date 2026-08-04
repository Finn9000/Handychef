<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('pickup_time')->nullable()->after('started_at');
            $table->string('pickup_location')->nullable()->after('pickup_time');
            $table->text('customization_notes')->nullable()->after('pickup_location');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['pickup_time', 'pickup_location', 'customization_notes']);
        });
    }
};
