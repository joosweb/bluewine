<?php

use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = \Faker\Factory::create();

        for($i=0; $i<=500; $i++):
            DB::table('items')
                ->insert([
                    'fk_user_id' => 1,
                    'code' => $faker->randomNumber($nbDigits = 9, $strict = false),
                    'name' => $faker->name,
                    'category_id' => rand(4,10),
                    'price' => $faker->randomNumber($nbDigits = 3, $strict = false),
                    'photo' => 'https://dummyimage.com/150',
                    'stock' => rand(1,45),
                ]);
        endfor;
    }
}
