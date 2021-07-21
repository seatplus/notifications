<?php

namespace Database\Factories;

use Seatplus\Notifications\Models\Outbox;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutboxFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Outbox::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
