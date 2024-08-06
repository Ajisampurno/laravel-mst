<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TSalesDetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $harga_bandrol = $faker->randomFloat(2, 10000, 100000);
            $qty = $faker->numberBetween(1, 10);
            $diskon_pct = $faker->randomFloat(2, 0, 30); // Diskon maksimal 30%
            $diskon_nilai = ($diskon_pct / 100) * $harga_bandrol;
            $harga_diskon = $harga_bandrol - $diskon_nilai;
            $total = $harga_diskon * $qty;

            DB::table('t_sales_dets')->insert([
                'sales_id' => $faker->numberBetween(1, 10),  // Asumsi ada 10 sales
                'barang_id' => $faker->numberBetween(1, 100),  // Asumsi ada 100 barang
                'harga_bandrol' => $harga_bandrol,
                'qty' => $qty,
                'diskon_pct' => $diskon_pct,
                'diskon_nilai' => $diskon_nilai,
                'harga_diskon' => $harga_diskon,
                'total' => $total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
