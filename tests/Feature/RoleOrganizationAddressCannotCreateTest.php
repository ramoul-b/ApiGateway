<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleOrganizationAddressCannotCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_and_not_have_permission_cannot_create_organization_address_role()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Organization Address Role',
            'code' => 'ORG_ADDR_ROLE',
            'organization_id' => 1, 
        'requestable' => 0, 
        'organization_address_id' => 1 
        ]);

        $response->assertStatus(403);
    }
}
