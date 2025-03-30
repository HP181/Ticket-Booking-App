@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Additional Payment Required</h1>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Booking Update Details</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $booking->event->title }}</h4>
                    <p><strong>Date:</strong> {{ $booking->event->event_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}</p>
                    <p><strong>Ticket Type:</strong> {{ $booking->ticketType->name }}</p>
                    
                    <div class="alert alert-info">
                        <p><strong>Current Quantity:</strong> {{ $booking->quantity }}</p>
                        <p><strong>New Quantity:</strong> {{ $newQuantity }}</p>
                        <p><strong>Additional Tickets:</strong> {{ $newQuantity - $booking->quantity }}</p>
                        <p><strong>Additional Amount Due:</strong> ${{ number_format($priceDifference, 2) }}</p>
                    </div>
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
                        <form action="{{ route('payment.process.stripe.additional', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Pay with Credit Card</button>
                        </form>
                        
                        <form action="{{ route('payment.process.additional', $booking->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" value="paypal">
                            <button type="submit" class="btn btn-info w-100">Pay with PayPal</button>
                        </form>
                        
                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-outline-secondary">Back to Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection