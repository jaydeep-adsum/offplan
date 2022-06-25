<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
        $array=array(
            [
            'name' => 'admin',
            'email' => 'admin@adsumdemo.com',
            'phone' => '1234567890',
            'password' => Hash::make('admin123'),
            'role' => 1,
            ],
            [
            'name' => 'agent',
            'email' => 'agent@adsumdemo.com',
            'phone' => '1234567890',
            'password' => Hash::make('admin123'),
            'role' => 2 ,
            ]
        );
        foreach ($array as $data) {
            $user = User::create($data);
        }
    }
}
