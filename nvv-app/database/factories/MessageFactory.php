<?php

declare(strict_types = 1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $from = rand(1, 30);
            $to = rand(1, 30);
            $is_read = rand(0, 1);
        } while ($from === $to);

        return [
            'from' => $from,
            'to' => $to,
            'message' => $this->faker->sentence,
            'is_read' => $is_read
        ];
    }


}
