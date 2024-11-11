<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RoleOrganizationCannotCreateTest extends TestCase
{
    
    
    use RefreshDatabase;

    public function test_authenticated_and_not_have_permission_cannot_create_organization_role()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Organization Role',
            'code' => 'ORG_ROLE',
            'organization_id' => 1, // Assurez-vous que cet ID est valide et existant
        'requestable' => true, // Ce champ doit être inclus si requis
        'organization_address_id' => 1 // Incluez ce champ si nécessaire
        ]);

        $response->assertStatus(403); 
    }
}
