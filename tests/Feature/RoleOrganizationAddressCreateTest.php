<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class RoleOrganizationAddressCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_and_have_permission_can_create_organization_address_role()
{
    $user = User::where('email', 'tnadminateca@gigaservizi.it')->first();
    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/roles', [
        'name' => 'Organization Address Role',
        'code' => 'ORG_ADDR_ROLE',
        'organization_id' => 1, 
        'requestable' => 0, 
        'organization_address_id' => 1 
    ]);

    $response->assertStatus(201);  // Ajusté à 200
    $this->assertDatabaseHas('roles', ['name' => 'Organization Address Role']);
}

}
