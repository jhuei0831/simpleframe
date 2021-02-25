<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed
{
    public function run()
    {
        $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'name'          => $faker->userName,
                'email'         => $faker->email,
                'password'      => sha1($faker->password),
                'role'          => 1,
            ];
        }

        $this->table('users')->insert($data)->saveData();
    }
}
