<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($eventId, $ticketTypeId)
    {
        $event = Event::findOrFail($eventId);
        $ticketType = TicketType::findOrFail($ticketTypeId);
        
        return view('bookings.create', compact('event', 'ticketType'));
    }

    public function store(Request $request, $eventId, $ticketTypeId)
    {
        $event = Event::findOrFail($eventId);
        $ticketType = TicketType::findOrFail($ticketTypeId);
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $ticketType->available_quantity,
        ]);

        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Create booking
            $booking = Booking::create([
                'user_id' => auth()->id(),
                'event_id' => $eventId,
                'ticket_type_id' => $ticketTypeId,
                'quantity' => $request->quantity,
                'status' => 'pending',
                'total_amount' => $ticketType->price * $request->quantity,
            ]);
            
            // Reduce available tickets
            $ticketType->available_quantity -= $request->quantity;
            $ticketType->save();
            
            DB::commit();
            
            return redirect()->route('payment.create', $booking->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while creating your booking. Please try again.');
        }
    }

    public function show($id)
    {
        $booking = Booking::with(['event', 'ticketType', 'payment'])
                         ->where('user_id', auth()->id())
                         ->findOrFail($id);
        
        return view('bookings.show', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::with(['event', 'ticketType'])
                          ->where('user_id', auth()->id())
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        return view('bookings.index', compact('bookings'));
    }

    public function cancel($id)
    {
        $booking = Booking::where('user_id', auth()->id())
                         ->where('status', 'pending')
                         ->findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            // Update booking status
            $booking->status = 'cancelled';
            $booking->save();
            
            // Restore ticket availability
            $ticketType = TicketType::findOrFail($booking->ticket_type_id);
            $ticketType->available_quantity += $booking->quantity;
            $ticketType->save();
            
            // Create notification
            Notification::create([
                'user_id' => auth()->id(),
                'type' => 'booking_cancelled',
                'message' => "Your booking for {$booking->event->title} has been cancelled.",
            ]);
            
            DB::commit();
            
            return redirect()->route('bookings.index')->with('success', 'Booking cancelled successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while cancelling your booking. Please try again.');
        }
    }

    public function verifyBooking($id)
    {
        $booking = Booking::with(['event', 'ticketType'])
                         ->where('status', 'confirmed')
                         ->findOrFail($id);
        
        // Here we would typically verify the booking against the event entry system
        
        return response()->json([
            'status' => 'success',
            'booking' => $booking,
        ]);
    }
}
