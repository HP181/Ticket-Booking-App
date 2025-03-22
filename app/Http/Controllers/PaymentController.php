<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Notification;
use App\Mail\BookingConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create($bookingId)
    {
        $booking = Booking::with(['event', 'ticketType'])
                         ->where('user_id', auth()->id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);
        
        return view('payments.create', compact('booking'));
    }

    public function processStripePayment(Request $request, $bookingId)
    {
        $booking = Booking::with(['event', 'ticketType'])
                         ->where('user_id', auth()->id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);
        
        // Set your secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $booking->event->title . ' - ' . $booking->ticketType->name,
                        ],
                        'unit_amount' => $booking->ticketType->price * 100, // Stripe uses cents
                    ],
                    'quantity' => $booking->quantity,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['bookingId' => $booking->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', ['bookingId' => $booking->id]),
            ]);
            
            return redirect($session->url);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
    }

    public function processPayPalPayment(Request $request, $bookingId)
    {
        // For MVP, we'll simulate PayPal payments
        // In a real app, we would integrate with PayPal SDK here
        
        $booking = Booking::with(['event', 'ticketType'])
                         ->where('user_id', auth()->id())
                         ->where('status', 'pending')
                         ->findOrFail($bookingId);
        
        // Simulate successful payment
        return $this->handleSuccessfulPayment($booking->id, 'paypal', 'SIMULATED-' . uniqid());
    }

    public function stripeSuccess(Request $request, $bookingId)
    {
        // Verify the payment with Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        try {
            $sessionId = $request->query('session_id');
            $session = Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                return $this->handleSuccessfulPayment($bookingId, 'stripe', $session->id);
            } else {
                return redirect()->route('payment.create', $bookingId)
                                ->with('error', 'Payment was not completed. Please try again.');
            }
        } catch (\Exception $e) {
            return redirect()->route('payment.create', $bookingId)
                            ->with('error', 'An error occurred while verifying your payment: ' . $e->getMessage());
        }
    }

    public function cancel($bookingId)
    {
        return redirect()->route('payment.create', $bookingId)
                        ->with('info', 'Payment was cancelled. You can try again when ready.');
    }

    private function handleSuccessfulPayment($bookingId, $paymentMethod, $transactionId)
    {
        // Change from findOrFail to where()->first() to ensure a single model is returned
        $booking = Booking::with(['event', 'ticketType', 'user'])
                         ->where('id', $bookingId)
                         ->first();

        if (!$booking) {
            throw new \Exception("Booking not found");
        }
        
        DB::beginTransaction();
        
        try {
            // Update booking status
            $booking->status = 'confirmed';
            $booking->save();
            
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'amount' => $booking->total_amount,
                'currency' => 'USD',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            // Create notification
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'booking_confirmed',
                'message' => "Your booking for {$booking->event->title} has been confirmed.",
            ]);
            
            DB::commit();
            
            // Send email confirmation
            Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
            
            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Payment completed successfully! Your booking is now confirmed.');

        } catch (\Exception $e) {
           DB::rollBack();
            return redirect()->route('payment.create', $booking->id)
                            ->with('error', 'An error occurred while processing your payment: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                // Find the booking by transaction ID (stored in metadata)
                if (isset($paymentIntent->metadata->booking_id)) {
                    // Change from find to where()->first() to ensure a single model is returned
                    $booking = Booking::with(['user', 'event', 'ticketType'])
                                     ->where('id', $paymentIntent->metadata->booking_id)
                                     ->first();
                    
                    if ($booking) {
                        $booking->status = 'confirmed';
                        $booking->save();
                        
                        // Create payment record if it doesn't exist
                        if (!$booking->payment) {
                            Payment::create([
                                'booking_id' => $booking->id,
                                'payment_method' => 'stripe',
                                'transaction_id' => $paymentIntent->id,
                                'amount' => $booking->total_amount,
                                'currency' => strtoupper($paymentIntent->currency),
                                'status' => 'completed',
                                'payment_date' => now(),
                            ]);
                        }
                        
                        // Send email notification
                        Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
                    }
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                if (isset($paymentIntent->metadata->booking_id)) {
                    $booking = Booking::where('id', $paymentIntent->metadata->booking_id)->first();
                    if ($booking) {
                        $booking->status = 'payment_failed';
                        $booking->save();
                    }
                }
                break;
        }

        return response()->json(['success' => true]);
    }
}