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
        if (Schema::hasTable('places') && !Schema::hasColumn('places', 'guide_info')) {
            Schema::table('places', function (Blueprint $table) {
                $table->longText('guide_info')->nullable()->after('placeAddress');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('places') && Schema::hasColumn('places', 'guide_info')) {
            Schema::table('places', function (Blueprint $table) {
                $table->dropColumn('guide_info');
            });
        }
    }
};
