<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['status' => 'Needs Changes'],
            ['status' => 'Rejected'],
            ['status' => 'Published'],
            ['status' => 'Pending Review'],
            ['status' => 'Pending Approval'],
            ['status' => 'Pending Publishing'],
        ];

        DB::table('review_statuses')->insert($statuses);
    }
}
