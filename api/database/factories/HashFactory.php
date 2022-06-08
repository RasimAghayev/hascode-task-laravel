<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hash>
 */
class HashFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
  protected $model = Hash::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'data' => $this->faker->text(),
            'hash' =>hash('sha1',$this->faker->text())
          ];
    }
}
