<?php

namespace Tests\Unit\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use App\Models\TicketType;

class EventTest extends TestCase
{
    use DatabaseTransactions;

    public function test_event_belongs_to_user()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $event->user);
        $this->assertEquals($user->id, $event->user->id);
    }
    
    public function test_event_has_many_ticket_types()
    {
        $event = Event::factory()->create();
        TicketType::factory()->count(3)->create(['event_id' => $event->id]);
        
        $this->assertCount(3, $event->ticketTypes);
        $this->assertInstanceOf(TicketType::class, $event->ticketTypes->first());
    }
    
    public function test_event_date_is_carbon_instance()
    {
        $event = Event::factory()->create([
            'event_date' => '2023-12-31'
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $event->event_date);
        $this->assertEquals('2023-12-31', $event->event_date->format('Y-m-d'));
    }
    
    public function test_is_published_is_boolean()
    {
        $event = Event::factory()->create([
            'is_published' => true
        ]);
        
        $this->assertIsBool($event->is_published);
        $this->assertTrue($event->is_published);
    }
}