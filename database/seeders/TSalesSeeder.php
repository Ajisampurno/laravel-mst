<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $subtotal = $faker->randomFloat(2, 10000, 1000000);
            $diskon = $faker->randomFloat(2, 0, $subtotal * 0.3);  // Diskon maksimal 30% dari subtotal
            $ongkir = $faker->randomFloat(2, 10000, 50000);
            $total_bayar = $subtotal - $diskon + $ongkir;

            DB::table('t_sales')->insert([
                'kode' => $faker->unique()->numerify('SALE#####'),
                'tgl' => $faker->dateTimeThisYear(),
                'cust_id' => $faker->numberBetween(1, 5),  // Asumsi ada 100 customer
                'subtotal' => $subtotal,
                'diskon' => $diskon,
                'ongkir' => $ongkir,
                'total_bayar' => $total_bayar,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
