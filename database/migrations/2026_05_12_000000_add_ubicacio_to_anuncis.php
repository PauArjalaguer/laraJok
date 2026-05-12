<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anuncis', function (Blueprint $table) {
            $table->decimal('latitud', 10, 8)->nullable()->after('id_tipus');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            $table->string('nom_ubicacio', 200)->nullable()->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('anuncis', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud', 'nom_ubicacio']);
        });
    }
};