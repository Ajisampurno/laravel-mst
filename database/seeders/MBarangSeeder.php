<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('m_barangs')->insert([
                'kode' => $faker->unique()->numerify('BRG###'),
                'nama' => $faker->name,
                'harga' => $faker->randomFloat(2, 1000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
