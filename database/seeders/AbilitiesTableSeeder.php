<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ability;
use App\Models\Role;
use App\Models\Permission;

class AbilitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Abilities.csv"), "r");
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            if ($firstline || empty($data[0])) {
                $firstline = false;
                continue;
            }

            $permissionCode = $data[1]; 
            $permission = Permission::where('code', $permissionCode)->first();
            //\Log::info(print_r($permissionCode, true));

            $roleCode = $data[0]; 
            $role = Role::where('code', $roleCode)->first();
            //\Log::info(print_r($roleCode, true));

            if (!$permission || !$role) {
                continue;
            }

            try {
                Ability::create([
                    'role_id' => $role->id,
                    'permission_id' => $permission->id,
                ]);
            } catch (\Exception $e) {
                //\Log::error("Error creating Account: " . $e->getMessage());
            }
        }

        fclose($csvFile);

    }
}
