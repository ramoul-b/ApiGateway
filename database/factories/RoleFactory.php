<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        // Supposons que vous avez des organisations avec des ID de 1 à 10 dans la base de données
        return [
            'name' => $this->faker->word,
            'code' => $this->faker->unique()->word,
            'requestable' => $this->faker->boolean,
            'organization_id' => $this->faker->numberBetween(1, 10),
            'organization_address_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
