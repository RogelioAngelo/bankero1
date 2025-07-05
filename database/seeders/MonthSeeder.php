<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $months = [
            ['id' => 1, 'name'=>'January'],
            ['id' => 2, 'name'=>'February'],
            ['id' => 3, 'name'=>'March'],
            ['id' => 4, 'name'=>'April'],
            ['id' => 5, 'name'=>'May'],
            ['id' => 6, 'name'=>'June'],
            ['id' => 7, 'name'=>'July'],
            ['id' => 8, 'name'=>'August'],
            ['id' => 9, 'name'=>'September'],
            ['id' => 10, 'name'=>'October'],
            ['id' => 11, 'name'=>'November'],
            ['id' => 12, 'name'=>'December']
        ];
        DB::table('month_names')->truncate(); // Clear the table before inserting
        DB::table('month_names')->insert($months);
    }
}
