<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        'name' => 'Admin Test',
        'email' => 'admin@test.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
]);
        User::create([
        'name' => 'Employee Test',
        'email' => 'employee@test.com',
        'password' => Hash::make('password'),
        'role' => 'employee',
        'kuota_cuti' => 12,
]);
    }
}
