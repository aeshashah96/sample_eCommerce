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
            'first_name'=>'admin2',
            'last_name'=>'admin2',
            'email'=>'admin2@gmail.com',
            // 'admin_logo' => 'userLogo.png',
            'password'=>Hash::make('123456'),
            'phone_number'=>6353454561,
            // 'admin_logo_url'=>url('upload/Admin/admin_logo/userLogo.png')
        ]);
    }
}
