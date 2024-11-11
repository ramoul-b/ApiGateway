<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            RolesTableSeeder::class,
            PermissionsTableSeeder::class,
            MicroservicesTableSeeder::class,
            ApisTableSeeder::class,
            AbilitiesTableSeeder::class,
            AccountsTableSeeder::class,
            ApiConditionsSeeder::class,
            PassportClientSeeder::class,

        ]);
    }
}
