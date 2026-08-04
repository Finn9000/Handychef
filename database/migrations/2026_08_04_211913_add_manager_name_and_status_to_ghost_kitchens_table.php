<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ghost_kitchens', function (Blueprint $table) {
            $table->string('manager_name')->nullable()->after('business_name');
            $table->string('status')->default('pending')->after('phone');
        });

        // Kitchens that existed before the approval workflow was added are already trusted.
        DB::table('ghost_kitchens')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('ghost_kitchens', function (Blueprint $table) {
            $table->dropColumn(['manager_name', 'status']);
        });
    }
};
