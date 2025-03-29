<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Booking;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        $booking = Booking::factory()->create();
        
        return [
            'booking_id' => $booking->id,
            'payment_method' => $this->faker->randomElement(['stripe', 'paypal']),
            'transaction_id' => $this->faker->uuid,
            'amount' => $booking->total_amount,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_date' => $this->faker->dateTimeThisMonth,
        ];
    }
}