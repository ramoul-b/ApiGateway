<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleCreationWithoutPermissionTest extends TestCase
{
    //use RefreshDatabase;

    /** @test */
    public function authenticated_user_without_permission_cannot_create_role()
    {
        $user = User::where('email', 'tnuser1@gigaservizi.it')->first();
        //dd($user);
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Unauthorized Role',
            'code' => 'NO_ACCESS',
            'requestable' => false,
            'organization_id' => 1,
            'organization_address_id' => 1,
        ]);

        $response->assertStatus(403); 
    }

}
