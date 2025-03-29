<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalBookings = Booking::count();
        
        return view('admin.dashboard', compact('totalUsers', 'totalEvents', 'totalBookings'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function editUserRole($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit-user-role', compact('user'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,organizer,attendee',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User role updated successfully');
    }

    public function events()
    {
        $events = Event::with('user')->paginate(10);
        return view('admin.events', compact('events'));
    }

    public function updateEventStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $event = Event::findOrFail($id);
        $event->is_published = $request->status;
        $event->save();

        return redirect()->route('admin.events')->with('success', 'Event status updated successfully');
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'event', 'ticketType'])->paginate(10);
        return view('admin.bookings', compact('bookings'));
    }

    public function generateReport(Request $request)
{
    $eventStats = Event::selectRaw('COUNT(*) as total')
                      ->selectRaw('SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published')
                      ->first();
    
    $bookingStats = Booking::selectRaw('COUNT(*) as total')
                          ->selectRaw('SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed')
                          ->selectRaw('SUM(total_amount) as revenue')
                          ->first();
    
    // If date range is provided, generate period-specific stats
    if ($request->has('start_date') && $request->has('end_date')) {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $periodStats = new \stdClass();
        $periodStats->events_created = Event::whereBetween('created_at', [$startDate, $endDate])->count();
        $periodStats->events_happening = Event::whereBetween('event_date', [$startDate, $endDate])->count();
        $periodStats->bookings_created = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $periodStats->revenue_generated = Booking::whereBetween('created_at', [$startDate, $endDate])
                                            ->where('status', 'confirmed')
                                            ->sum('total_amount');
        
        return view('admin.report', compact('eventStats', 'bookingStats', 'periodStats'));
    }
    
    return view('admin.report', compact('eventStats', 'bookingStats'));
}
}
