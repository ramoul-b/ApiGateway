<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'role_id' => function () {
                return \App\Models\Role::factory()->create()->id;
            },
            'anagrafica_id' => $this->faker->randomNumber(),
            'anagrafica_address_id' => $this->faker->randomNumber(),
            'default' => $this->faker->boolean(),
            'using' => $this->faker->boolean(),
        ];
    }
}
