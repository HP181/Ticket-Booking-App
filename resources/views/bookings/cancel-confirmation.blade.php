@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Cancel Booking</h1>
    
    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Confirm Cancellation</h5>
                </div>
                <div class="card-body">
                    <p>Are you sure you want to cancel your booking for:</p>
                    
                    <div class="alert alert-info">
                        <h4>{{ $booking->event->title }}</h4>
                        <p><strong>Date:</strong> {{ $booking->event->event_date->format('F j, Y') }}</p>
                        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->event->end_time)->format('g:i A') }}</p>
                        <p><strong>Ticket Type:</strong> {{ $booking->ticketType->name }}</p>
                        <p><strong>Quantity:</strong> {{ $booking->quantity }}</p>
                        <p><strong>Total Amount:</strong> ${{ number_format($booking->total_amount, 2) }}</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <p><strong>Note:</strong> Cancellation may be subject to our refund policy. Please check the event details for specific terms.</p>
                    </div>
                    
                    <form action="{{ route('bookings.cancel-booking', $booking->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('This action cannot be undone. Are you sure you want to cancel this booking?')">Confirm Cancellation</button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-secondary">Back to Booking</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection