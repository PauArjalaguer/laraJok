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
        Schema::dropIfExists('anuncis_fotos');
        Schema::dropIfExists('anuncis');
        Schema::dropIfExists('anuncis_estats');
        Schema::dropIfExists('anuncis_mides');
        Schema::dropIfExists('anuncis_tipus');
        Schema::dropIfExists('anuncis_marques');
        Schema::dropIfExists('anuncis_colors');
        Schema::dropIfExists('anuncis_categories');
    
        // Primer crear les taules independents
        Schema::create('anuncis_estats', function (Blueprint $table) {
            $table->bigIncrements('id_estat');
            $table->string('estat');
        });
    
        Schema::create('anuncis_mides', function (Blueprint $table) {
            $table->bigIncrements('id_mida'); // arreglat: era 'id_mides'
            $table->string('mida');
        });
    
        Schema::create('anuncis_tipus', function (Blueprint $table) {
            $table->bigIncrements('id_tipus');
            $table->string('tipus');
        });
    
        Schema::create('anuncis_marques', function (Blueprint $table) {
            $table->bigIncrements('id_marca');
            $table->string('marca');
        });
    
        Schema::create('anuncis_colors', function (Blueprint $table) {
            $table->bigIncrements('id_color'); // arreglat: hi havia un espai
            $table->string('color');
            $table->timestamps();
        });
    
        Schema::create('anuncis_categories', function (Blueprint $table) {
            $table->bigIncrements('id_categoria');
            $table->string('categoria');
            $table->timestamps();
        });
    
        // Crear anuncis sense foreign keys
        Schema::create('anuncis', function (Blueprint $table) {
            $table->id();
            $table->string('titol');
            $table->text('descripcio');
            $table->decimal('preu', 8, 2);
            $table->unsignedBigInteger('id_estat');
            $table->unsignedBigInteger('id_marca');
            $table->unsignedBigInteger('id_mida');
            $table->unsignedBigInteger('id_usuari');
            $table->unsignedBigInteger('id_tipus');
            $table->string('ubicacio');
            $table->timestamps();
        });
    
        // Ara sí, afegim les foreign keys després
        Schema::table('anuncis', function (Blueprint $table) {
            $table->foreign('id_estat')->references('id_estat')->on('anuncis_estats')->onDelete('cascade');
            $table->foreign('id_marca')->references('id_marca')->on('anuncis_marques')->onDelete('cascade');
            $table->foreign('id_mida')->references('id_mida')->on('anuncis_mides')->onDelete('cascade');
            $table->foreign('id_usuari')->references('id')->on('users')->onDelete('cascade'); // assumint 'users.id'
            $table->foreign('id_tipus')->references('id_tipus')->on('anuncis_tipus')->onDelete('cascade');
        });
    
        // Ara crear anuncis_fotos (ja existeix anuncis)
        Schema::create('anuncis_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('foto_ruta');
            $table->unsignedBigInteger('id_anunci');
            $table->timestamps();
        });
    
        Schema::table('anuncis_fotos', function (Blueprint $table) {
            $table->foreign('id_anunci')->references('id')->on('anuncis')->onDelete('cascade');
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
