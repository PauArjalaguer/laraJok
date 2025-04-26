<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AdvertismentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('anuncis_mides')->insert([
            
            ['mida' => 'XS'], ['mida' => 'S'], ['mida' => 'M'], 
            ['mida' => 'L'], ['mida' => 'XL'], ['mida' => 'XXL'],
            ['mida' => 'XXXL'], ['mida' => '4XL'],
            ['mida' => '3-4'], ['mida' => '5-6'], ['mida' => '7-8'], // Infantils
            ['mida' => '35'], ['mida' => '36'], ['mida' => '37'],
            ['mida' => '38'], ['mida' => '39'], ['mida' => '40'],
            ['mida' => '41'], ['mida' => '42'], ['mida' => '43'],
            ['mida' => '44'], ['mida' => '45'], ['mida' => '46'], 
            ['mida' => '47'], ['mida' => '48'],['mida' => 'Altres']
        ]);
        
        DB::table('anuncis_tipus')->insert([
            ['tipus' => 'Patins'], ['tipus' => 'Estics'],
            ['tipus' => 'Cascs'], ['tipus' => 'Guants'],
            ['tipus' => 'Genolleres'], ['tipus' => 'Colzeres'],
            ['tipus' => 'Pantalons'], ['tipus' => 'Samarretes'],
            ['tipus' => 'Mitgetes'], ['tipus' => 'Tibialeres'],
            ['tipus' => 'Motxilles'], ['tipus' => 'Bosses esportives'],
            ['tipus' => 'Dessuadores'], ['tipus' => 'Conjunts'],
            ['tipus' => 'Roba interior tècnica'], ['tipus' => 'Proteccions'],
            ['tipus' => 'Accessoris'], ['tipus' => 'Eines de manteniment'],
            ['tipus' => 'Porteries'], 
            ['tipus' => 'Stick bags'], ['tipus' => 'Entrenadors'],['mida' => 'Altres']
        ]);

        DB::table('anuncis_marques')->insert([
            ['marca' => 'Reno'], ['marca' => 'Azemad'],
            ['marca' => 'Etisport'], ['marca' => 'Bauer'],
            ['marca' => 'Genial'], ['marca' => 'Reno'],
            ['marca' => 'Replic'], ['marca' => 'Crojet'],
            ['marca' => 'Meneghini'], ['marca' => 'Sioux'],
            ['marca' => 'Toor'], ['marca' => 'Barovari'],
            ['marca' => 'QSkate'], ['marca' => 'Roll-line'],
            ['marca' => 'McRoller'], ['marca' => 'Sitka'], ['marca' => 'Barovari'],['mida' => 'Altres']
        ]);

        DB::table('anuncis_colors')->insert([
            ['color' => 'Negre'], ['color' => 'Blanc'], ['color' => 'Vermell'],
            ['color' => 'Blau'], ['color' => 'Verd'], ['color' => 'Groc'],
            ['color' => 'Taronja'], ['color' => 'Rosa'], ['color' => 'Lila'],
            ['color' => 'Gris'], ['color' => 'Marró'], ['color' => 'Beix'],
            ['color' => 'Turquesa'], ['color' => 'Burgundy'], ['color' => 'Multicolor'],['mida' => 'Altres']
        ]);
        
        DB::table('anuncis_estats')->insert([
            ['estat' => 'Nou'], ['estat' => 'Com nou'],
            ['estat' => 'Usat'], ['estat' => 'Amb senyals d\'ús'],
            ['estat' => 'Per reparar o peces'],['mida' => 'Altres']
        ]);
        
        DB::table('anuncis_categories')->insert([
            ['categoria' => 'Infantil'], ['categoria' => 'Juvenil'],
            ['categoria' => 'Adult'], ['categoria' => 'Femení'],
            ['categoria' => 'Masculí'], ['categoria' => 'Unisex'],['mida' => 'Altres']
        ]);
        
    }
}
