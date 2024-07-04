<?php

namespace Database\Seeders;

use App\Models\Admin;
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
            'first_name'=>'admin',
            'last_name'=>'admin',
            'email'=>'admin@gmail.com',
            'admin_logo' => 'userLogo.png',
            'password'=>Hash::make('11111111'),
            'phone_number'=>6353454561,
            'admin_logo_url'=>url('upload/Admin/admin_logo/userLogo.png')
        ]);
    }
}
