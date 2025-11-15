<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a user first
        $user1 = User::create([
            'email' => 'cahya@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('cahya123'),
            'role' => 'alumni',
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user2 = User::create([
            'email' => 'alex@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('alex123'),
            'role' => 'alumni',
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $user3 = User::create([
            'email' => 'fadhil@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('fadhil123'),
            'role' => 'alumni',
            'remember_token' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Then create the associated alumni record
        Alumni::create([
            'user_id' => $user1->id,
            'nama' => 'Cahya Ilham',
            'nomor_telepon' => '081234567890',
            'fakultas' => 'Fateta',
            'angkatan' => '60',
        ]);

        Alumni::create([
            'user_id' => $user2->id,
            'nama' => 'Rafi Alexander',
            'nomor_telepon' => '081230987564',
            'fakultas' => 'Vokasi',
            'angkatan' => '60',
        ]);

        Alumni::create([
            'user_id' => $user3->id,
            'nama' => 'Faris Fadhil',
            'nomor_telepon' => '08987654321',
            'fakultas' => 'Vokasi',
            'angkatan' => '60',
        ]);
    }
}
