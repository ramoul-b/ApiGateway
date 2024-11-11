<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleCreationWithPermissionTest extends TestCase
{
    //use RefreshDatabase;

    /** @test */
    public function user_with_permission_can_create_role()
    {
        $this->seed();
        $user = User::where('email', 'tnsuperadmin@gigaservizi.it')->first();
        //dump($user); 

    if (!$user) {
        $this->fail("No user found with the email tnsuperadmin@gigaservizi.it");
    }
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'New Admin Role',
            'code' => 'ADMIN_ROLE',
            'requestable' => true,
            'organization_id' => 1,
            'organization_address_id' => 1,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('roles', ['name' => 'New Admin Role']);
    }
}
