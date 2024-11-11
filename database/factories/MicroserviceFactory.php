<?php

namespace Database\Factories;

use App\Models\Microservice;
use Illuminate\Database\Eloquent\Factories\Factory;

class MicroserviceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Microservice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'code' => $this->faker->unique()->word, 
            'secret_key' => $this->faker->word,
            'main_ipv4' => $this->faker->ipv4,
            'load_balancer_ipv4' => $this->faker->ipv4,
            'main_ipv6' => $this->faker->ipv6,
            'load_balancer_ipv6' => $this->faker->ipv6,
        ];
    }
}
