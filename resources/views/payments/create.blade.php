@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Complete Payment</h1>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Booking Details</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $booking->event->title }}</h4>
                    <p><strong>Date:</strong> {{ $booking->event->event_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}</p>
                    <p><strong>Ticket Type:</strong> {{ $booking->ticketType->name }}</p>
                    <p><strong>Quantity:</strong> {{ $booking->quantity }}</p>
                    <p><strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Payment Options</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <form action="{{ route('payment.process.stripe', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Pay with Credit Card</button>
                        </form>
                        
                        <form action="{{ route('payment.process.paypal', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">Pay with PayPal</button>
                        </form>
                        
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection