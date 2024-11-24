<?php

namespace Database\Seeders;

use App\Models\Researchcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearchcategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Researchcategory::create([
            'name' => 'Technical Research'
        ]);

        Researchcategory::create([
            'name' => 'Social Research'
        ]);

        Researchcategory::create([
            'name' => 'Technological Research'
        ]);

        Researchcategory::create([
            'name' => 'Extension'
        ]);
    }
}
