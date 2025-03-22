<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Mail\BookingUpdated;
use App\Mail\BookingCancelled;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;

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

    public function edit($id)
    {
        $booking = Booking::with(['event', 'ticketType'])
                         ->where('id', $id)
                         ->where('user_id', auth()->id())
                         ->where('status', 'confirmed')
                         ->first();
        
        if (!$booking) {
            return redirect()->route('bookings.index')
                           ->with('error', 'Booking not found or you do not have permission to edit it.');
        }
        
        // Check if the event date has passed
        if ($booking->event->event_date < now()->startOfDay()) {
            return redirect()->route('bookings.show', $booking->id)
                           ->with('error', 'Cannot edit a booking for a past event.');
        }
        
        return view('bookings.edit', compact('booking'));
    }

public function update(Request $request, $id)
{
    // Get a single booking model instead of a collection
    $booking = Booking::with(['event', 'ticketType'])
                     ->where('id', $id)
                     ->where('user_id', auth()->id())
                     ->where('status', 'confirmed')
                     ->first();
    
    if (!$booking) {
        return redirect()->route('bookings.index')
                       ->with('error', 'Booking not found or you do not have permission to edit it.');
    }
    
    // Check if the event date has passed
    if ($booking->event->event_date < now()->startOfDay()) {
        return redirect()->route('bookings.show', $booking->id)
                       ->with('error', 'Cannot edit a booking for a past event.');
    }
    
    $request->validate([
        'quantity' => [
            'required', 
            'integer', 
            'min:1',
            function ($attribute, $value, $fail) use ($booking) {
                $ticketType = $booking->ticketType;
                $currentlyAvailable = $ticketType->available_quantity + $booking->quantity;
                
                if ($value > $currentlyAvailable) {
                    $fail("Only {$currentlyAvailable} tickets are available.");
                }
            },
        ],
    ]);
    
    // Calculate price difference
    $oldTotalAmount = $booking->total_amount;
    $newTotalAmount = $booking->ticketType->price * $request->quantity;
    $priceDifference = $newTotalAmount - $oldTotalAmount;
    
    DB::beginTransaction();
    
    try {
        // Update ticket type available quantity
        $ticketType = $booking->ticketType;
        $ticketType->available_quantity += $booking->quantity - $request->quantity;
        $ticketType->save();
        
        // Update booking
        $booking->quantity = $request->quantity;
        $booking->total_amount = $newTotalAmount;
        $booking->save();
        
        // Create notification
        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'booking_updated',
            'message' => "Your booking for {$booking->event->title} has been updated.",
        ]);
        
        // If there's a price difference and it's positive, create a new payment record
        if ($priceDifference > 0) {
            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'update_charge',
                'transaction_id' => 'UPDATE-' . uniqid(),
                'amount' => $priceDifference,
                'currency' => 'USD',
                'status' => 'completed',
                'payment_date' => now(),
            ]);
        } else if ($priceDifference < 0) {
            // In a real application, you would process a refund here
            // For MVP, we'll just record it
            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'refund',
                'transaction_id' => 'REFUND-' . uniqid(),
                'amount' => abs($priceDifference),
                'currency' => 'USD',
                'status' => 'completed',
                'payment_date' => now(),
            ]);
        }
        
        DB::commit();
        
        // Send email confirmation
        Mail::to($booking->user->email)->send(new BookingUpdated($booking));
        
        return redirect()->route('bookings.show', $booking->id)
                       ->with('success', 'Booking updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'An error occurred while updating your booking: ' . $e->getMessage());
    }
}

public function cancelConfirmation($id)
{
    $booking = Booking::with(['event', 'ticketType'])
                     ->where('id', $id)
                     ->where('user_id', auth()->id())
                     ->where('status', 'confirmed')
                     ->first();
    
    if (!$booking) {
        return redirect()->route('bookings.index')
                       ->with('error', 'Booking not found or you do not have permission to cancel it.');
    }
    
    // Check if the event date has passed
    if ($booking->event->event_date < now()->startOfDay()) {
        return redirect()->route('bookings.show', $booking->id)
                       ->with('error', 'Cannot cancel a booking for a past event.');
    }
    
    return view('bookings.cancel-confirmation', compact('booking'));
}

public function cancelBooking(Request $request, $id)
{
    // Get a single booking model instead of a collection
    $booking = Booking::with(['event', 'ticketType', 'user'])
                     ->where('id', $id)
                     ->where('user_id', auth()->id())
                     ->where('status', 'confirmed')
                     ->first();
    
    if (!$booking) {
        return redirect()->route('bookings.index')
                       ->with('error', 'Booking not found or you do not have permission to cancel it.');
    }
    
    // Check if the event date has passed
    if ($booking->event->event_date < now()->startOfDay()) {
        return redirect()->route('bookings.show', $booking->id)
                       ->with('error', 'Cannot cancel a booking for a past event.');
    }
    
    DB::beginTransaction();
    
    try {
        // Store booking data for email
        $bookingCopy = clone $booking;
        
        // Update ticket type available quantity
        $ticketType = $booking->ticketType;
        $ticketType->available_quantity += $booking->quantity;
        $ticketType->save();
        
        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();
        
        // Create notification
        Notification::create([
            'user_id' => auth()->id(),
            'type' => 'booking_cancelled',
            'message' => "Your booking for {$booking->event->title} has been cancelled.",
        ]);
        
        // In a real application, you would process a refund here based on cancellation policy
        // For MVP, we'll just record it
        Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'refund',
            'transaction_id' => 'CANCEL-' . uniqid(),
            'amount' => $booking->total_amount,
            'currency' => 'USD',
            'status' => 'completed',
            'payment_date' => now(),
        ]);
        
        DB::commit();
        
        // Send email confirmation
        Mail::to($booking->user->email)->send(new BookingCancelled($bookingCopy));
        
        return redirect()->route('bookings.index')
                       ->with('success', 'Booking cancelled successfully. A refund will be processed according to our cancellation policy.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'An error occurred while cancelling your booking: ' . $e->getMessage());
    }
}
}
