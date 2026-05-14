<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anuncis', function (Blueprint $table) {
            $table->boolean('conforme_usuari_enviament_mail')->default(false)->after('nom_ubicacio');
            $table->unsignedInteger('visites')->default(0)->after('conforme_usuari_enviament_mail');
            $table->unsignedInteger('enviaments')->default(0)->after('visites');
        });
    }

    public function down(): void
    {
        Schema::table('anuncis', function (Blueprint $table) {
            $table->dropColumn(['conforme_usuari_enviament_mail', 'visites', 'enviaments']);
        });
    }
};