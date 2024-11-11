<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Api;
use App\Models\Microservice;

class ApisTableSeeder extends Seeder
{
    public function run()
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Apis.csv"), "r");

        $firstline = true;
        // Skip the header row
       // fgetcsv($csvFile, 2000, ",");

        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            // Log the data to check the array structure
            //\Log::info(print_r($data, true));
    
            $msCode = $data[0]; 
            $microservice = Microservice::where('code', $msCode)->first();


            //\Log::info(print_r($microservice, true));


            if ($firstline || !$data[0]) {
                $firstline = false;
                continue;
            }
            try {
                
                Api::create([
                    'microservice_id' => $microservice->id,
                    'route_in' => $data[1],
                    'method' => $data[2],
                ]);
            } catch (\Exception $e) {
                \Log::error("Error creating Api: " . $e->getMessage());
            }

            
        }

        fclose($csvFile);
    }
}
