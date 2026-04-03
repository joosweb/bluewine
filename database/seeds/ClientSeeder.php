<?php

use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = \Faker\Factory::create();

        for($i=0; $i<=10; $i++):
            DB::table('providers')
                ->insert([
                    'run' => $faker->unique()->ean8,
                    'verifying_digit' => rand(1,9),
                    'name' => $faker->name,
                    'last_name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->e164PhoneNumber,
                    'fk_user_id' => 1
                ]);
        endfor;
    }
}
