<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('anuncis_estats', function (Blueprint $table) {
            $table->id();
            $table->string('estat');
        });

        Schema::create('anuncis_mides', function (Blueprint $table) {
            $table->id();
            $table->string('mida');
        });

        Schema::create('anuncis_tipus', function (Blueprint $table) {
            $table->id();
            $table->string('tipus');
        });

        Schema::create('anuncis_marques', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
        });

        Schema::create('anuncis', function (Blueprint $table) {
            $table->id();
            $table->string('titol');
            $table->text('descripcio');
            $table->decimal('preu', 8, 2);
            $table->foreignId('estat')->constrained('anuncis_estats')->cascadeOnDelete();
            $table->foreignId('marca')->constrained('anuncis_marques')->cascadeOnDelete();
            $table->foreignId('mida')->constrained('anuncis_mides')->cascadeOnDelete();
            $table->foreignId('id_usuari')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_anuncis_tipus')->constrained('anuncis_tipus')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('anuncis_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('foto_ruta');
            $table->foreignId('id_anunci')->constrained('anuncis')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('anuncis_fotos');
        Schema::dropIfExists('anuncis');
        Schema::dropIfExists('anuncis_marques');
        Schema::dropIfExists('anuncis_tipus');
        Schema::dropIfExists('anuncis_mides');
        Schema::dropIfExists('anuncis_estats');
    }
};
