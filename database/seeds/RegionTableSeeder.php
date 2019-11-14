<?php

use Illuminate\Database\Seeder;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Region
        DB::table('regions')->truncate();

        // Register a new Region
        DB::table('regions')->insert([
            ['name' => 'Arusha'],
            ['name' => 'Dar es Salaam'],
            ['name' => 'Dodoma'],
            ['name' => 'Geita'],
            ['name' => 'Iringa'],
            ['name' => 'Kagera'],
            ['name' => 'Katavi'],
            ['name' => 'Kigoma'],
            ['name' => 'Kilimanjaro'],
            ['name' => 'Lindi'],
            ['name' => 'Manyara'],
            ['name' => 'Mara'],
            ['name' => 'Mbeya'],
            ['name' => 'Mjini Magharibi'],
            ['name' => 'Morogoro'],
            ['name' => 'Mtwara'],
            ['name' => 'Mwanza'],
            ['name' => 'Njombe'],
            ['name' => 'Pemba North'],
            ['name' => 'Pemba South'],
            ['name' => 'Pwani'],
            ['name' => 'Rukwa'],
            ['name' => 'Ruvuma'],
            ['name' => 'Shinyanga'],
            ['name' => 'Simiyu'],
            ['name' => 'Singida'],
            ['name' => 'Tabora'],
            ['name' => 'Tanga'],
            ['name' => 'Unguja North'],
            ['name' => 'Unguja South'],
        ]);
    }
}
