<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Nanoco Admin',
            'email' => 'nanoco@gmail.com',
            'password' => Hash::make('123456'),
            'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9uPZu8PPXaNdiyOKa_Z19joZj4Us3XGYdxQ&usqp=CAU',
        ]);
        
        User::create([
            'name' => 'Ngoc Huy',
            'email' => 'ngochuy@gmail.com',
            'password' => Hash::make('123456'),
            'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcScuQGyYbgV9HFyiunO9mF6_lnB6MYwcx6t3w&usqp=CAU',
        ]);

        User::create([
            'name' => 'Robert Pham',
            'email' => 'robert@gmail.com',
            'password' => Hash::make('123456'),
            'avatar' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqGQ8dQ-LMiMmTEyBijR0FzpQHC7tH6qTE2g&usqp=CAU',
        ]);

        foreach(range(1,5) as $index) {
            User::factory()->create();
        }
    }
}
