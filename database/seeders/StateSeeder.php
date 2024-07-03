<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            //india
            [
               'state_name'=>'Gujarat',
                'country_id'=>'1'
            ],
            [
                'state_name'=>'Tamil Nadu',
                 'country_id'=>'1'
            ],
             [
                'state_name'=>'Karnataka',
                 'country_id'=>'1'
             ],
             [
                'state_name'=>'Maharashtra',
                 'country_id'=>'1'
             ],
             [
                'state_name'=>'Uttar Pradesh',
                 'country_id'=>'1'
             ],
             [
                'state_name'=>'Bihar',
                 'country_id'=>'1'
             ],
             //japan
             [
                'state_name'=>'Kyoto',
                 'country_id'=>'2'
             ],
             [
                 'state_name'=>'Kyushu',
                  'country_id'=>'2'
             ],
              [
                 'state_name'=>'Shikoku',
                  'country_id'=>'2'
              ],
              [
                 'state_name'=>'Tohoku',
                  'country_id'=>'2'
              ],
              [
                 'state_name'=>'Hiroshima',
                  'country_id'=>'2'
              ],
              [
                 'state_name'=>'Osaka',
                  'country_id'=>'2'
              ],
              //USA
              [
                'state_name'=>'Minnesote',
                 'country_id'=>'3'
             ],
             [
                 'state_name'=>'Vermont',
                  'country_id'=>'3'
             ],
              [
                 'state_name'=>'Florida',
                  'country_id'=>'3'
              ],
              [
                 'state_name'=>'New Hampshire',
                  'country_id'=>'3'
              ],
              [
                 'state_name'=>'Idaho',
                  'country_id'=>'3'
              ],
        ];
        foreach($users as $user){
            State::create($user);
        }
    }
}
