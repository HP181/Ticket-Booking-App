<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use App\Models\User;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $startTime = $this->faker->time('H:i');
        $endTime = date('H:i', strtotime($startTime) + 3600); // Add 1 hour
        
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'image' => null,
            'location' => $this->faker->address,
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_published' => $this->faker->boolean,
        ];
    }
}