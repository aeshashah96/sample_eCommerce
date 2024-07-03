<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'country_name'=>'India',
                'country_code'=>'IN'
            ],
            [
                'country_name'=>'Japan',
                'country_code'=>'JA'

            ],
            [
                'country_name'=>'America',
                'country_code'=>'USA'

            ],
        ];
        foreach($users as $user){
            Country::create($user);
        }
    }
}
