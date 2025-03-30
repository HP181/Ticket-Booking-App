<?php

namespace Tests\Feature\Event;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EventCRUDTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $organizer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        
        // Create an organizer user
        $this->organizer = User::factory()->create([
            'role' => 'organizer'
        ]);
    }

    public function test_organizer_can_view_create_event_form()
    {
        $response = $this->actingAs($this->organizer)
                         ->get(route('events.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('events.create');
    }

    public function test_organizer_can_create_event()
    {
        $eventData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->address,
            'event_date' => now()->addDays(15)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '16:00',
            'image' => UploadedFile::fake()->image('event.jpg')
        ];

        $response = $this->actingAs($this->organizer)
                         ->post(route('events.store'), $eventData);
        
        $this->assertDatabaseHas('events', [
            'title' => $eventData['title'],
            'user_id' => $this->organizer->id
        ]);
        
        $event = Event::where('title', $eventData['title'])->first();
        $response->assertRedirect(route('events.edit', $event->id));
    }

    public function test_organizer_can_edit_their_event()
    {
        // Create an event
        $event = Event::factory()->create([
            'user_id' => $this->organizer->id,
            'title' => 'Original Title'
        ]);

        $updatedData = [
            'title' => 'Updated Title',
            'description' => $event->description,
            'location' => $event->location,
            'event_date' => $event->event_date->format('Y-m-d'),
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
        ];

        $response = $this->actingAs($this->organizer)
                         ->put(route('events.update', $event->id), $updatedData);
        
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Updated Title'
        ]);
        
        $response->assertRedirect(route('organizer.events'));
    }

    public function test_organizer_can_delete_their_event()
    {
        // Create an event
        $event = Event::factory()->create([
            'user_id' => $this->organizer->id
        ]);

        $response = $this->actingAs($this->organizer)
                         ->delete(route('events.destroy', $event->id));
        
        $this->assertDatabaseMissing('events', [
            'id' => $event->id
        ]);
        
        $response->assertRedirect(route('organizer.events'));
    }

    public function test_organizer_cannot_edit_others_events()
    {
        // Create another organizer
        $otherOrganizer = User::factory()->create([
            'role' => 'organizer'
        ]);
        
        // Create an event owned by the other organizer
        $event = Event::factory()->create([
            'user_id' => $otherOrganizer->id
        ]);

        $response = $this->actingAs($this->organizer)
                         ->get(route('events.edit', $event->id));
        
        $response->assertStatus(404);
    }

    public function test_publish_unpublish_event()
    {
        // Create an event
        $event = Event::factory()->create([
            'user_id' => $this->organizer->id,
            'is_published' => false
        ]);

        // Test publishing
        $response = $this->actingAs($this->organizer)
                         ->put(route('events.update-status', $event->id));
        
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_published' => true
        ]);
        
        // Test unpublishing
        $response = $this->actingAs($this->organizer)
                         ->put(route('events.update-status', $event->id));
        
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_published' => false
        ]);
    }
}