<?php

use Illuminate\Database\Seeder;
use App\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
			'user_name' => 'Rishab',
			'email' => 'rishabtest01@yopmail.com',
			'password' => Hash::make('123'),
		]);
    }
}
