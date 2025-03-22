@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                @if($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}">
                @endif
                <div class="card-body">
                    <h1 class="card-title">{{ $event->title }}</h1>
                    <p class="card-text">{!! nl2br(e($event->description)) !!}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-calendar"></i> Date & Time</h5>
                            <p>
                                {{ $event->event_date->format('F j, Y') }}<br>
                                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-geo-alt"></i> Location</h5>
                            <p>{{ $event->location }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tickets</h5>
                </div>
                <div class="card-body">
                    @forelse($event->ticketTypes as $ticket)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">{{ $ticket->name }}</h5>
                                <p class="card-text">{{ $ticket->description }}</p>
                                <p class="card-text">
                                    <strong>Price:</strong> ${{ number_format($ticket->price, 2) }}<br>
                                    <strong>Available:</strong> {{ $ticket->available_quantity }} tickets
                                </p>
                                @auth
                                    @if($ticket->available_quantity > 0)
                                        <a href="{{ route('bookings.create', ['eventId' => $event->id, 'ticketTypeId' => $ticket->id]) }}" class="btn btn-primary">Book Now</a>
                                    @else
                                        <button class="btn btn-secondary" disabled>Sold Out</button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Login to Book</a>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <p class="text-center">No tickets available for this event.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection