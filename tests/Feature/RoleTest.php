<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Log;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_create_a_role()
    {
        // Exécution des seeders pour préparer la base de données
        $this->seed();
        Log::info('Database seeded');

        // Recherche de l'utilisateur par email
        $user = User::where('email', 'tnsuperadmin@gigaservizi.it')->first();
        if (!$user) {
            $this->fail('Utilisateur prévu pour le test introuvable.');
        }

        // Authentification via Passport
        Passport::actingAs($user);
        Log::info('Starting role creation test.');

        // Requête POST pour créer un nouveau rôle
        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Example Role',
            'code' => 'EXAMPLE_CODE',
            'requestable' => false,  
            'organization_id' => 1,
            'organization_address_id' => 1,
        ]);

        // Vérification de la réponse
        $response->assertStatus(201); 
        $response->assertJson([       
            'name' => 'Example Role',
            'code' => 'EXAMPLE_CODE',
            'requestable' => false,
            'organization_id' => 1,
            'organization_address_id' => 1
        ]);

        // Vérification de la présence des données dans la base de données
        $this->assertDatabaseHas('roles', [
            'name' => 'Example Role',
            'code' => 'EXAMPLE_CODE',
            'organization_id' => 1,
            'organization_address_id' => 1,
            'requestable' => false
        ]);
    }
}
