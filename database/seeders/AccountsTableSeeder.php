<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("storage/app/public/Factory/FACTORY_init_rev00_Accounts.csv"), "r");
        $firstline = true;
    
        while (($data = fgetcsv($csvFile, 2000, ";")) !== FALSE) {
            if ($firstline || empty($data[0])) {
                $firstline = false;
                continue;
            }
    
            $userCode = $data[1];
            $user = User::where('username', $userCode)->first();
    
            if (!$user) {
                // Si l'utilisateur n'existe pas, vous pouvez choisir de le créer ici ou ignorer cette entrée
                //\Log::warning("User not found with username: $userCode");
                continue;
            }
    
            $roleCode = $data[2];
            $role = Role::where('code', $roleCode)->first();
    
            if (!$role) {
                // Si le rôle n'existe pas, vous pouvez choisir de le créer ici ou ignorer cette entrée
                //\Log::warning("Role not found with code: $roleCode");
                continue;
            }
    
            try {
                Account::create([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                    'anagrafica_id' => $data[3],
                    'anagrafica_address_id' => $data[4],
                    'default' => $data[5],
                    'using' => $data[6] == '0' ? 0 : 1,
                ]);
            } catch (\Exception $e) {
                \Log::error("Error creating Account: " . $e->getMessage());
            }
        }
    
        fclose($csvFile);
    }
    
}
