<?php

namespace Tests\Feature\Booking;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Booking;

class BookingTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $attendee;
    protected $event;
    protected $ticketType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an attendee user
        $this->attendee = User::factory()->create([
            'role' => 'attendee'
        ]);
        
        // Create an organizer
        $organizer = User::factory()->create([
            'role' => 'organizer'
        ]);
        
        // Create an event
        $this->event = Event::factory()->create([
            'user_id' => $organizer->id,
            'is_published' => true,
            'event_date' => now()->addDays(10)->format('Y-m-d')
        ]);
        
        // Create a ticket type
        $this->ticketType = TicketType::factory()->create([
            'event_id' => $this->event->id,
            'quantity' => 100,
            'available_quantity' => 100,
            'price' => 25.00
        ]);
    }

    public function test_user_can_view_booking_page()
    {
        $response = $this->actingAs($this->attendee)
                         ->get(route('bookings.create', [
                             'eventId' => $this->event->id,
                             'ticketTypeId' => $this->ticketType->id
                         ]));
        
        $response->assertStatus(200);
        $response->assertViewIs('bookings.create');
    }

    public function test_user_can_create_booking()
    {
        $bookingData = [
            'quantity' => 2
        ];

        $response = $this->actingAs($this->attendee)
                         ->post(route('bookings.store', [
                             'eventId' => $this->event->id,
                             'ticketTypeId' => $this->ticketType->id
                         ]), $bookingData);
        
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->attendee->id,
            'event_id' => $this->event->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 2,
            'status' => 'pending'
        ]);
        
        // Check if available quantity is updated
        $updatedTicketType = TicketType::find($this->ticketType->id);
        $this->assertEquals(98, $updatedTicketType->available_quantity);
    }

    public function test_user_can_view_booking_details()
    {
        // Create a booking
        $booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $this->event->id,
            'ticket_type_id' => $this->ticketType->id,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->attendee)
                         ->get(route('bookings.show', $booking->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('bookings.show');
        $response->assertViewHas('booking');
    }

    public function test_user_can_cancel_booking()
    {
        // Create a booking
        $booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $this->event->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 3,
            'status' => 'confirmed'
        ]);
        
        // Update ticket type available quantity to simulate tickets were taken
        $this->ticketType->available_quantity -= 3;
        $this->ticketType->save();
        
        $initialAvailableQuantity = $this->ticketType->available_quantity;

        $response = $this->actingAs($this->attendee)
                         ->delete(route('bookings.cancel-booking', $booking->id));
        
        // Check if booking status is updated
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled'
        ]);
        
        // Check if tickets are restored to available inventory
        $updatedTicketType = TicketType::find($this->ticketType->id);
        $this->assertEquals($initialAvailableQuantity + 3, $updatedTicketType->available_quantity);
    }

    public function test_user_can_update_booking_quantity()
    {
        // Create a booking
        $booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $this->event->id,
            'ticket_type_id' => $this->ticketType->id,
            'quantity' => 2,
            'status' => 'confirmed',
            'total_amount' => 50.00
        ]);
        
        // Update ticket type available quantity to simulate tickets were taken
        $this->ticketType->available_quantity -= 2;
        $this->ticketType->save();

        $updateData = [
            'quantity' => 4  // Increase to 4 tickets
        ];

        $response = $this->actingAs($this->attendee)
                         ->put(route('bookings.update', $booking->id), $updateData);
        
        // Check if booking quantity is updated
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'quantity' => 4,
            'total_amount' => 100.00  // 4 tickets at $25 each
        ]);
        
        // Check if available quantity is updated correctly (2 additional tickets taken)
        $updatedTicketType = TicketType::find($this->ticketType->id);
        $this->assertEquals($this->ticketType->available_quantity - 2, $updatedTicketType->available_quantity);
    }
}