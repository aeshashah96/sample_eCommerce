<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities=[
            [
                'city_name'=>'Ahmedabad',
                'state_id'=>1,
            ],
            [
                'city_name'=>'Rajkot',
                'state_id'=>1,
            ],
            [
                'city_name'=>'Anand',
                'state_id'=>1,
            ],
            [
                'city_name'=>'Chennai',
                'state_id'=>2,
            ],
            [
                'city_name'=>'Salem',
                'state_id'=>2,
            ],
            [
                'city_name'=>'Vellore',
                'state_id'=>2,
            ],
            [
                'city_name'=>'Bengaluru',
                'state_id'=>3,
            ],
            [
                'city_name'=>'Mangaluru',
                'state_id'=>3,
            ],
            [
                'city_name'=>'Mumbai',
                'state_id'=>4,
            ],
            [
                'city_name'=>'Nagpur',
                'state_id'=>4,
            ],
            [
                'city_name'=>'Mirzapur',
                'state_id'=>5,
            ],
            [
                'city_name'=>'Lucknow',
                'state_id'=>5,
            ],
            [
                'city_name'=>'patna',
                'state_id'=>6,
            ],
            [
                'city_name'=>'Bhagalpur',
                'state_id'=>6,
            ],
        ];
        foreach($cities as $city){
            City::create($city);
        }
    }
}
