<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Importer la classe Log
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Roles.csv"), "r");
        
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            // Log the data to check the array structure
            //\Log::info(print_r($data, true));
    
            if ($firstline || !$data[0]) {
                $firstline = false;
                continue;
            }

            try {
                $role = Role::create([
                    'name' => $data[1],
                    'code' => $data[2],
                    'organization_id' => $data[3],
                    'organization_address_id' => $data[4],
                ]);
                
                // Log the SQL query executed
                //Log::debug("SQL query executed: " . DB::getQueryLog()[0]['query']);
            } catch (\Exception $e) {
                Log::error("Error creating Role: " . $e->getMessage());
            }
        }
    }
}
