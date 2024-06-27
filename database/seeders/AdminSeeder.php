<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'first_name'=>'harsh',
            'last_name'=>'zala',
            'email'=>'harsh@gmail.com',
            'password'=>Hash::make('123'),
            'phoneNumber'=>6353454561,
        ]);
    }
}
