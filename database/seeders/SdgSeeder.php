<?php

namespace Database\Seeders;

use App\Models\Sdg;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SdgSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sdg::create([
            'name' =>'01 - No Poverty',
            'sdgimg_id'=> 1
        ]);

        Sdg::create([
            'name' =>'02 - Zero Hunger',
            'sdgimg_id'=> 2
        ]);

        Sdg::create([
            'name' =>'03 - Good Health & Well-Being',
            'sdgimg_id'=> 3
        ]);

        Sdg::create([
            'name' =>'04 - Quality Education',
            'sdgimg_id'=> 4
        ]);

        Sdg::create([
            'name' =>'05 - Gender Equality',
            'sdgimg_id'=> 5
        ]);

        Sdg::create([
            'name' =>'06 - Clean Water & Sanitation',
            'sdgimg_id'=> 6
        ]);

        Sdg::create([
            'name' =>'07 - Affordable & Clean Energy',
            'sdgimg_id'=> 7
        ]);

        Sdg::create([
            'name' =>'08 - Decent Work & Economic Growth',
            'sdgimg_id'=> 8
        ]);

        Sdg::create([
            'name' =>'09 - Industry, Inovation, & Infrastructure',
            'sdgimg_id'=> 9
        ]);

        Sdg::create([
            'name' =>'10 - Reduced Inequalities',
            'sdgimg_id'=> 10
        ]);

        Sdg::create([
            'name' =>'11 - Sustainable Cities & Communitites',
            'sdgimg_id'=> 11
        ]);

        Sdg::create([
            'name' =>'12 - Responsible Consumption & Production',
            'sdgimg_id'=> 12
        ]);

        Sdg::create([
            'name' =>'13 - Climate Action',
            'sdgimg_id'=> 13
        ]);

        Sdg::create([
            'name' =>'14 - Life Below Water',
            'sdgimg_id'=> 14
        ]);

        Sdg::create([
            'name' =>'15 - Life on Land',
            'sdgimg_id'=> 15
        ]);

        Sdg::create([
            'name' =>'16 - Peace, Justice, & Strong Institutions',
            'sdgimg_id'=> 16
        ]);

        Sdg::create([
            'name' =>'17 - Partnerships For the Goals',
            'sdgimg_id'=> 17
        ]);
    }
}
