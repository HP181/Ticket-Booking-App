<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:organizer')->except(['index', 'show']);
    }

    public function index()
    {
        $events = Event::where('is_published', true)
                      ->whereDate('event_date', '>=', now())
                      ->orderBy('event_date', 'asc')
                      ->paginate(9);
        
        return view('events.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with('ticketTypes')->findOrFail($id);
        
        return view('events.show', compact('event'));
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date|after:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');
        $data['user_id'] = auth()->id();
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        $event = Event::create($data);

        return redirect()->route('events.edit', $event->id)->with('success', 'Event created successfully. Now add ticket types.');
    }

    public function edit($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        $ticketTypes = TicketType::where('event_id', $id)->get();
        
        return view('events.edit', compact('event', 'ticketTypes'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            
            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        $event->update($data);

        return redirect()->route('organizer.events')->with('success', 'Event updated successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        
        $event->is_published = !$event->is_published;
        $event->save();

        $status = $event->is_published ? 'published' : 'unpublished';
        return redirect()->route('organizer.events')->with('success', "Event $status successfully");
    }

    public function destroy($id)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($id);
        
        // Delete the event image if exists
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        $event->delete();

        return redirect()->route('organizer.events')->with('success', 'Event deleted successfully');
    }

    // Ticket Type Management
    public function createTicketType($eventId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        
        return view('events.create-ticket-type', compact('event'));
    }

    public function storeTicketType(Request $request, $eventId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        TicketType::create([
            'event_id' => $eventId,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'available_quantity' => $request->quantity,
        ]);

        return redirect()->route('events.edit', $eventId)->with('success', 'Ticket type added successfully');
    }

    public function editTicketType($eventId, $ticketTypeId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $ticketType = TicketType::where('event_id', $eventId)->findOrFail($ticketTypeId);
        
        return view('events.edit-ticket-type', compact('event', 'ticketType'));
    }

    public function updateTicketType(Request $request, $eventId, $ticketTypeId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $ticketType = TicketType::where('event_id', $eventId)->findOrFail($ticketTypeId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:' . ($ticketType->quantity - $ticketType->available_quantity),
        ]);

        // Calculate the difference in quantity
        $quantityDifference = $request->quantity - $ticketType->quantity;
        
        $ticketType->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'available_quantity' => $ticketType->available_quantity + $quantityDifference,
        ]);

        return redirect()->route('events.edit', $eventId)->with('success', 'Ticket type updated successfully');
    }

    public function destroyTicketType($eventId, $ticketTypeId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $ticketType = TicketType::where('event_id', $eventId)->findOrFail($ticketTypeId);
        
        // Check if there are any bookings for this ticket type
        if ($ticketType->bookings()->count() > 0) {
            return redirect()->route('events.edit', $eventId)->with('error', 'Cannot delete ticket type with existing bookings');
        }
        
        $ticketType->delete();

        return redirect()->route('events.edit', $eventId)->with('success', 'Ticket type deleted successfully');
    }
}
