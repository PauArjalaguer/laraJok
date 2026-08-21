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
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'match_tries')) {
                $table->unsignedTinyInteger('match_tries')->default(0)->after('idMatch')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'match_tries')) {
                $table->dropColumn('match_tries');
            }
        });
    }
};
