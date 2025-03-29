<?php

namespace Tests\Feature\Payment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Booking;
use App\Models\Payment;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $attendee;
    protected $booking;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an attendee user
        $this->attendee = User::factory()->create([
            'role' => 'attendee'
        ]);
        
        // Create an event with ticket type
        $organizer = User::factory()->create(['role' => 'organizer']);
        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'is_published' => true
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50.00
        ]);
        
        // Create a pending booking
        $this->booking = Booking::factory()->create([
            'user_id' => $this->attendee->id,
            'event_id' => $event->id,
            'ticket_type_id' => $ticketType->id,
            'quantity' => 2,
            'total_amount' => 100.00,
            'status' => 'pending'
        ]);
    }

    public function test_user_can_view_payment_page()
    {
        $response = $this->actingAs($this->attendee)
                         ->get(route('payment.create', $this->booking->id));
        
        $response->assertStatus(200);
        $response->assertViewIs('payments.create');
    }

    public function test_user_can_make_paypal_payment()
    {
        // For the MVP, we're simulating PayPal payments
        $response = $this->actingAs($this->attendee)
                         ->post(route('payment.process.paypal', $this->booking->id));
        
        // Check if booking status updated
        $this->assertDatabaseHas('bookings', [
            'id' => $this->booking->id,
            'status' => 'confirmed'
        ]);
        
        // Check if payment record created
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'payment_method' => 'paypal',
            'amount' => 100.00,
            'status' => 'completed'
        ]);
    }

    public function test_payment_redirect_for_unpaid_booking()
    {
        // Create another user
        $otherUser = User::factory()->create();
        
        // Try to access payment page for another user's booking
        $response = $this->actingAs($otherUser)
                         ->get(route('payment.create', $this->booking->id));
        
        // Should redirect or return 404
        $response->assertStatus(404);
    }

    public function test_payment_creates_notification()
    {
        $this->actingAs($this->attendee)
             ->post(route('payment.process.paypal', $this->booking->id));
        
        // Check if notification is created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->attendee->id,
            'type' => 'booking_confirmed'
        ]);
    }
}