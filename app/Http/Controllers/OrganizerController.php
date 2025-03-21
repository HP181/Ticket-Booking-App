<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:organizer');
    }

    public function dashboard()
    {
        $events = Event::where('user_id', auth()->id())->get();
        $eventIds = $events->pluck('id')->toArray();
        
        $totalEvents = $events->count();
        $publishedEvents = $events->where('is_published', true)->count();
        $totalBookings = Booking::whereIn('event_id', $eventIds)->count();
        $totalRevenue = Booking::whereIn('event_id', $eventIds)
                             ->where('status', 'confirmed')
                             ->sum('total_amount');
        
        return view('organizer.dashboard', compact('totalEvents', 'publishedEvents', 'totalBookings', 'totalRevenue'));
    }

    public function events()
    {
        $events = Event::where('user_id', auth()->id())->paginate(10);
        return view('organizer.events', compact('events'));
    }

    public function eventBookings($eventId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        $bookings = Booking::where('event_id', $eventId)->with('user', 'ticketType')->paginate(10);
        
        return view('organizer.event-bookings', compact('event', 'bookings'));
    }

    public function generateEventReport($eventId)
    {
        $event = Event::where('user_id', auth()->id())->findOrFail($eventId);
        
        $bookingStats = Booking::where('event_id', $eventId)
                             ->selectRaw('COUNT(*) as total')
                             ->selectRaw('SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed')
                             ->selectRaw('SUM(total_amount) as revenue')
                             ->first();
        
        $ticketsSold = Booking::where('event_id', $eventId)
                            ->where('status', 'confirmed')
                            ->sum('quantity');
        
        return view('organizer.event-report', compact('event', 'bookingStats', 'ticketsSold'));
    }
}
