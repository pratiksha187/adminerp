<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChallanFactory extends Factory
{
    protected $model = \App\Models\Challan::class;

    public function definition()
    {
        return [
            'challan_no' => $this->faker->unique()->numerify('CH-####'),
            'date' => $this->faker->date(),
            'party_name' => $this->faker->company(),
            'material' => $this->faker->word(),
            'vehicle_no' => $this->faker->bothify('??###'),
            'measurement' => $this->faker->randomDigitNotNull(),
            'location' => $this->faker->city(),
            'time' => $this->faker->time('H:i'),
            'receiver_sign' => $this->faker->name(),
            'driver_sign' => $this->faker->name(),
        ];
    }
}
