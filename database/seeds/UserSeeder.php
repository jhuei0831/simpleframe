<?php

use Phinx\Seed\AbstractSeed;
use Kerwin\Core\Support\Toolbox;
class UserSeeder extends AbstractSeed
{
    public function run()
    {
        /* $faker = Faker\Factory::create();
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
                'id'            => Toolbox::UUIDv4(),
                'name'          => $faker->userName,
                'email'         => rand(1,20).$faker->email,
                'password'      => sha1($faker->password),
                'role'          => rand(1,2),
            ];
        }

        $this->table('users')->insert($data)->saveData(); */
    }
}
