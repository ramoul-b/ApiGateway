<?php

namespace Database\Factories;

use App\Models\Api;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Api::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'microservice_id' => function () {
                return \App\Models\Microservice::factory()->create()->id;
            },
            'route_in' => $this->faker->word,
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
        ];
    }
}
