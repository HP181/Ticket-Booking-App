<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TicketType;
use App\Models\Event;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition()
    {
        $quantity = $this->faker->numberBetween(50, 200);
        
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->word . ' Ticket',
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 10, 300),
            'quantity' => $quantity,
            'available_quantity' => $quantity,
        ];
    }
}