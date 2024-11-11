<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Microservice;

class MicroservicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Microservices.csv"), "r");
        
        $firstline = true;

        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            // Log the data to check the array structure
            //\Log::info(print_r($data, true));
    
            if ($firstline || !$data[0]) {
                $firstline = false;
                continue;
            }
            try {
                
                Microservice::create([
                    'name' => $data[0],
                    'code' => $data[1],
                    'secret_key' => $data[2],
                    'main_ipv4' => $data[3],
                    'load_balancer_ipv4' => $data[4],
                    'main_ipv6' => $data[5],
                    'load_balancer_ipv6' => $data[6],
                ]);
            } catch (\Exception $e) {
                \Log::error("Error creating Microservice: " . $e->getMessage());
            }

            
        }

    }
}
