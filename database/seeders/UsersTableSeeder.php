<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Users.csv"), "r");
    
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            // Log the data to check the array structure
            //\Log::info(print_r($data, true));
    
            if ($firstline || !$data[0]) {
                $firstline = false;
                continue;
            }
    
            // Verify that all expected columns are present
            if (count($data) < 5) {
                //\Log::warning("Not enough data in row: " . print_r($data, true));
                continue;
            }
    
            try {
                User::create([
                    'name' => $data[1],
                    'surname' => $data[2],
                    'email' => $data[3],
                    'username' => $data[4],
                    'password' => Hash::make($data[5]),
                ]);
            } catch (\Exception $e) {
                \Log::error("Error creating user: " . $e->getMessage());
            }
        }
    
        fclose($csvFile);
    }    
}
