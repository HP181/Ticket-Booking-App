@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Booking Details</h1>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Event Information</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $booking->event->title }}</h4>
                    <p><strong>Date:</strong> {{ $booking->event->event_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}</p>
                    <p><strong>Location:</strong> {{ $booking->event->location }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Booking Summary</h5>
                </div>
                <div class="card-body">
                    <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                    <p><strong>Status:</strong> 
                        @if($booking->status == 'confirmed')
                            <span class="badge bg-success">Confirmed</span>
                        @elseif($booking->status == 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($booking->status == 'cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                    </p>
                    <p><strong>Ticket Type:</strong> {{ $booking->ticketType->name }}</p>
                    <p><strong>Quantity:</strong> {{ $booking->quantity }}</p>
                    <p><strong>Price per Ticket:</strong> ${{ number_format($booking->ticketType->price, 2) }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                    
                    @if($booking->status == 'pending')
                        <div class="alert alert-warning">
                            Your booking is pending payment.
                            <a href="{{ route('payment.create', $booking->id) }}" class="btn btn-primary mt-2 d-block">Complete Payment</a>
                        </div>
                        
                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</button>
                        </form>
                    @elseif($booking->status == 'confirmed')
                        <div class="alert alert-success">
                            Your booking is confirmed. Please present your booking reference at the event.
                        </div>
                        
                        @if($booking->event->event_date >= now()->startOfDay())
                            <div class="mt-3">
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-primary w-100 mb-2">Edit Booking</a>
                                <a href="{{ route('bookings.cancel-confirmation', $booking->id) }}" class="btn btn-danger w-100">Cancel Booking</a>
                            </div>
                        @endif
                    @elseif($booking->status == 'cancelled')
                        <div class="alert alert-danger">
                            This booking has been cancelled.
                        </div>
                    @endif
                </div>
            </div>
            
            @if($booking->payment && $booking->status == 'confirmed')
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Payment Method:</strong> {{ ucfirst($booking->payment->payment_method) }}</p>
                        <p><strong>Transaction ID:</strong> {{ $booking->payment->transaction_id }}</p>
                        <p><strong>Payment Date:</strong> {{ $booking->payment->payment_date->format('F j, Y g:i A') }}</p>
                        <p><strong>Amount Paid:</strong> ${{ number_format($booking->payment->amount, 2) }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">Back to My Bookings</a>
        <a href="{{ route('events.index') }}" class="btn btn-primary">Browse More Events</a>
    </div>
</div>
@endsection