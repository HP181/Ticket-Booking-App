<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $ticketType = TicketType::factory()->create();
        $totalAmount = $quantity * $ticketType->price;
        
        return [
            'user_id' => User::factory(),
            'event_id' => $ticketType->event_id,
            'ticket_type_id' => $ticketType->id,
            'quantity' => $quantity,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'booking_reference' => 'BKG-' . Str::random(10),
            'total_amount' => $totalAmount,
        ];
    }
}