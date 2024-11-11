<?php

namespace Database\Factories;

use App\Models\Ability;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ability::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'role_id' => function () {
                return \App\Models\Role::factory()->create()->id;
            },
            'permission_id' => function () {
                return \App\Models\Permission::factory()->create()->id;
            },
        ];
    }
}
