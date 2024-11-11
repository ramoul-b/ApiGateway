<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;


class RoleOrganizationCreateTest extends TestCase
{
    
    use RefreshDatabase;

    public function test_authenticated_and_have_permission_can_create_organization_role()
    {
    $user = User::where('email', 'tnadminateca@gigaservizi.it')->first();
    Passport::actingAs($user);

    $response = $this->postJson('/api/v1/roles', [
        'name' => 'Organization Role',
        'code' => 'ORG_ROLE',
        'organization_id' => 1,
        'requestable' => 0,
        'organization_address_id' => 1
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('roles', ['name' => 'Organization Role']);
    }
}
