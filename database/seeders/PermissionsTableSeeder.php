<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PermissionCategory;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00.csv"), "r");
        
        $firstline = true;
        $rowNumber = 0;

        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            // Log the data to check the array structure
            //\Log::info(print_r($data, true));
            $rowNumber++;
            if ($firstline || !$data[0]) {
                $firstline = false;
                Log::info("Skipping header row");
                continue;
            }
            if (count($data) < 3) {
                Log::warning("Skipping row $rowNumber due to incorrect number of columns");
                continue;
            }
            try {
                $permissionCategory = PermissionCategory::firstOrCreate(
                    ['name' => trim($data[0])]
                );
                
                Permission::create([
                    'name' => $data[1],
                    'code' => $data[2],
                    'permission_category_id' => $permissionCategory->id,
                ]);
               // Log::info("Inserted permission '{$data[1]}' with code '{$data[2]}' for category '{$data[0]}' at row $rowNumber");

            } catch (\Exception $e) {
                \Log::error("Error creating Permission: " . $e->getMessage());
            }

            
        }
    }
}
