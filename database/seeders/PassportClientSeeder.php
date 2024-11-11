<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

class PassportClientSeeder extends Seeder
{
    public function run()
    {
        $clientRepository = new ClientRepository();
        $url = config('app.url');

        // Création du client Password Grant
        $clientRepository->createPasswordGrantClient(
            null, 'Password Grant Client', $url
        );

        // Création du client Personal Access
        $clientRepository->createPersonalAccessClient(
            null, 'Personal Access Client', $url
        );
    }
}
