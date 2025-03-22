@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Booking</h1>
    
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
                    <p><strong>Ticket Type:</strong> {{ $booking->ticketType->name }}</p>
                    <p><strong>Price per Ticket:</strong> ${{ number_format($booking->ticketType->price, 2) }}</p>
                    <p><strong>Currently Available:</strong> {{ $booking->ticketType->available_quantity }} tickets (plus your current {{ $booking->quantity }} tickets)</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Update Ticket Quantity</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Number of Tickets</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $booking->quantity) }}" min="1" max="{{ $booking->ticketType->available_quantity + $booking->quantity }}">
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Booking</button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection