<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'key'=>'LOGO',
                'value'=>url('/upload/logo/logo.png'),
            ],
            [
                'key'=>'INSTAGRAM',
                'value'=>url('/instagram'),
            ],
            [
                'key'=>'TWITTER',
                'value'=>url('/twitter'),
            ],
            [
                'key'=>'FACEBOOK',
                'value'=>url('facebook'),
            ],
            [
                'key'=>'ADDRESS',
                'value'=>'123 Street, New York, USA',
            ],
            [
                'key'=>'MAIL',
                'value'=>'info@example.com'
            ],
            [
                'key'=>'CONTACT',
                'value'=>'+012 345 67890'
            ],
            [
                'key'=>'GMAP',
                'value'=>'https://www.google.com/maps'
            ]
        ];
        foreach($data as $item){
            Setting::create($item);
        }
    }
}
