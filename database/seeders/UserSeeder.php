<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('admin');

        $users = [
            [
                'name' => 'Budi Setiawan',
                'acronym' => 'BS',
                'phone_number' => '0818205020',
                'email' => 'budibuilding2@yahoo.com',
                'username' => 'BS',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Alwi Hasan',
                'acronym' => 'AH',
                'phone_number' => '08122335455',
                'email' => 'alwihasan@yahoo.com',
                'username' => 'AH',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Adwin Aditya',
                'acronym' => 'AA',
                'phone_number' => '087823457573',
                'email' => 'adwinaditya@yahoo.com',
                'username' => 'AA',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Banda Arya',
                'acronym' => 'BA',
                'phone_number' => '0098234828394',
                'email' => 'HJASBDSA@GMAIL.COM',
                'username' => 'BA',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Aguston Yudomartono',
                'acronym' => 'AY',
                'phone_number' => '0834905234',
                'email' => '-@GMAIL.COM',
                'username' => 'AY',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Ardi Januardi',
                'acronym' => 'AJ',
                'phone_number' => '098324709',
                'email' => '--@GMAIL.COM',
                'username' => 'AJ',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ],
            [
                'name' => 'Arif MT',
                'acronym' => 'AMT',
                'phone_number' => '09782676535',
                'email' => '---@GMAIL.COM',
                'username' => 'AMT',
                'email_verified_at' => NULL,
                'password' => bcrypt('1234'),
            ]
        ];

        foreach ($users as $user) {
            $act = User::create($user);

            $act->assignRole('referee');
        }
    }
}
