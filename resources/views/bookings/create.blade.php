@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Book Tickets</h1>
    
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Event Details</h5>
                </div>
                <div class="card-body">
                    <h4>{{ $event->title }}</h4>
                    <p><strong>Date:</strong> {{ $event->event_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}</p>
                    <p><strong>Location:</strong> {{ $event->location }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Booking Summary</h5>
                </div>
                <div class="card-body">
                    <h5>{{ $ticketType->name }}</h5>
                    <p>{{ $ticketType->description }}</p>
                    <p><strong>Price:</strong> ${{ number_format($ticketType->price, 2) }} per ticket</p>
                    <p><strong>Available:</strong> {{ $ticketType->available_quantity }} tickets</p>
                    
                    <form action="{{ route('bookings.store', ['eventId' => $event->id, 'ticketTypeId' => $ticketType->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Number of Tickets</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="1" min="1" max="{{ $ticketType->available_quantity }}">
                            @error('quantity')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection