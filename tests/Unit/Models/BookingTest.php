<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Booking;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Payment;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_belongs_to_user()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($user->id, $booking->user->id);
    }
    
    public function test_booking_belongs_to_event()
    {
        $event = Event::factory()->create();
        $booking = Booking::factory()->create(['event_id' => $event->id]);
        
        $this->assertInstanceOf(Event::class, $booking->event);
        $this->assertEquals($event->id, $booking->event->id);
    }
    
    public function test_booking_belongs_to_ticket_type()
    {
        $ticketType = TicketType::factory()->create();
        $booking = Booking::factory()->create(['ticket_type_id' => $ticketType->id]);
        
        $this->assertInstanceOf(TicketType::class, $booking->ticketType);
        $this->assertEquals($ticketType->id, $booking->ticketType->id);
    }
    
    public function test_booking_has_one_payment()
    {
        $booking = Booking::factory()->create();
        $payment = Payment::factory()->create(['booking_id' => $booking->id]);
        
        $this->assertInstanceOf(Payment::class, $booking->payment);
        $this->assertEquals($payment->id, $booking->payment->id);
    }
    
    public function test_booking_has_unique_reference()
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();
        
        $this->assertNotEquals($booking1->booking_reference, $booking2->booking_reference);
    }
}