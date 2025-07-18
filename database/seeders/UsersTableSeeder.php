<?php

namespace Database\Seeders;

use App\Models\Admin\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       foreach (range(1, 100) as $i) {
            $email = "user$i@example.com";

            // Check if this email already exists
            if (!User::where('email', $email)->exists()) {
                User::create([
                    'name' => "User $i",
                    'email' => $email,
                    'password' => Hash::make('password'),
                ]);
            }
        }
    }
}
