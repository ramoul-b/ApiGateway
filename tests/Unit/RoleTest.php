<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_role_can_have_many_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();

        $role->permissions()->attach($permission1->id);
        $role->permissions()->attach($permission2->id);

        $this->assertCount(2, $role->permissions);
    }
}
