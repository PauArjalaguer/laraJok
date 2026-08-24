<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('places') && Schema::hasColumn('places', 'guide_info')) {
            DB::statement("ALTER TABLE `places` MODIFY `guide_info` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
        }

        if (Schema::hasTable('matches') && Schema::hasColumn('matches', 'cronica')) {
            DB::statement("ALTER TABLE `matches` MODIFY `cronica` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
