<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Taules lookup ──────────────────────────────────────────────────────
        Schema::create('anuncismarques', function (Blueprint $table) {
            $table->id();
            $table->string('nom_marca', 100);
            $table->timestamps();
        });

        Schema::create('anuncisestats', function (Blueprint $table) {
            $table->id();
            $table->string('nom_estat', 100);
            $table->timestamps();
        });

        Schema::create('anuncismides', function (Blueprint $table) {
            $table->id();
            $table->string('nom_mida', 20);
            $table->string('tipus_mida', 30); // 'samarreta' o 'calcat'
            $table->timestamps();
        });

        Schema::create('anuncistipus', function (Blueprint $table) {
            $table->id();
            $table->string('nom_tipus', 100);
            $table->string('icona_fa', 60)->nullable();
            $table->timestamps();
        });

        // ── Taula principal ────────────────────────────────────────────────────
        Schema::create('anuncis', function (Blueprint $table) {
            $table->id();
            $table->string('titol', 200);
            $table->text('descripcio');
            $table->decimal('preu', 8, 2)->nullable();
            $table->foreignId('id_usuari')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_marca')->constrained('anuncismarques')->onDelete('restrict');
            $table->foreignId('id_estat')->constrained('anuncisestats')->onDelete('restrict');
            $table->foreignId('id_mida')->constrained('anuncismides')->onDelete('restrict');
            $table->foreignId('id_tipus')->constrained('anuncistipus')->onDelete('restrict');
            $table->timestamps();
        });

        // ── Fotos ──────────────────────────────────────────────────────────────
        Schema::create('anuncisfotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_anunci')->constrained('anuncis')->onDelete('cascade');
            $table->string('foto_ruta', 500);
            $table->unsignedTinyInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anuncisfotos');
        Schema::dropIfExists('anuncis');
        Schema::dropIfExists('anuncistipus');
        Schema::dropIfExists('anuncismides');
        Schema::dropIfExists('anuncisestats');
        Schema::dropIfExists('anuncismarques');
    }
};
