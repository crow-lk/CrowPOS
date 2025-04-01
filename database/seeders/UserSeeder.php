<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'superadmin@crow.lk'
        ], [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email'=>'superadmin@crow.lk',
            'password' => bcrypt('Apple@123')
        ]);
    }
}
