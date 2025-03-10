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
            ['mida' => '35'], ['mida' => '36'], ['mida' => '37'],
            ['mida' => '38'], ['mida' => '39'], ['mida' => '40'],
            ['mida' => '41'], ['mida' => '42'], ['mida' => '43'],
            ['mida' => '44'], ['mida' => '45']
        ]);

        DB::table('anuncis_tipus')->insert([
            ['tipus' => 'Patins'], ['tipus' => 'Estics'],
            ['tipus' => 'Cascs'], ['tipus' => 'Guants'],
            ['tipus' => 'Genolleres'], ['tipus' => 'Pantalons'],
            ['tipus' => 'Samarretes'],['tipus' => 'Motxilles'],
        ]);

        DB::table('anuncis_marques')->insert([
            ['marca' => 'Reno'], ['marca' => 'Azemad'],
            ['marca' => 'Etisport'], ['marca' => 'Bauer'],
            ['marca' => 'Genial'], ['marca' => 'Reno'],
            ['marca' => 'Replic'], ['marca' => 'Crojet'],
            ['marca' => 'Meneghini'], ['marca' => 'Sioux'],
            ['marca' => 'Toor'], ['marca' => 'Barovari'],
            ['marca' => 'QSkate'], ['marca' => 'Roll-line'],
            ['marca' => 'McRoller'], ['marca' => 'Sitka']
        ]);
    }
}
