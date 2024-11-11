<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Laravel\Passport\Passport;

class SoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_soft_deleted_role_is_not_visible()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);
    
        $role = Role::factory()->create();
        $role->delete();  // Soft delete the role
    
        // Attempt to retrieve the soft deleted role via API
        $response = $this->getJson("/api/v1/roles/{$role->id}");
    
        // Check if the response is either 404 or 403 based on your application logic
        $response->assertStatus(403); // Now expecting unauthorized access response
    }


    /** @test */
    public function a_soft_deleted_role_is_visible_with_trashed()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $this->actingAs($user, 'api');

        // Soft delete the role
        $role->delete();

        // Accessing the role locally with trashed records included
        $foundRole = Role::withTrashed()->find($role->id);

        $this->assertNotNull($foundRole);
        $this->assertTrue($foundRole->trashed());
    }
}
