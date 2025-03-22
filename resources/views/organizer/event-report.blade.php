@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1>Event Report</h1>
            <h4 class="text-muted">{{ $event->title }}</h4>
        </div>
        <a href="{{ route('organizer.events') }}" class="btn btn-secondary">Back to Events</a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Event Details</div>
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $event->event_date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}</p>
                    <p><strong>Location:</strong> {{ $event->location }}</p>
                    <p><strong>Status:</strong> 
                        @if($event->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Unpublished</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">Ticket Sales by Type</div>
                <div class="card-body">
                    @if($event->ticketTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Ticket Type</th>
                                        <th>Price</th>
                                        <th>Total Quantity</th>
                                        <th>Sold</th>
                                        <th>Available</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->ticketTypes as $ticket)
                                        <tr>
                                            <td>{{ $ticket->name }}</td>
                                            <td>${{ number_format($ticket->price, 2) }}</td>
                                            <td>{{ $ticket->quantity }}</td>
                                            <td>{{ $ticket->quantity - $ticket->available_quantity }}</td>
                                            <td>{{ $ticket->available_quantity }}</td>
                                            <td>${{ number_format(($ticket->quantity - $ticket->available_quantity) * $ticket->price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No ticket types found for this event.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Booking Statistics</div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Total Bookings</h5>
                        <h2>{{ $bookingStats->total ?? 0 }}</h2>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Confirmed Bookings</h5>
                        <h2>{{ $bookingStats->confirmed ?? 0 }}</h2>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Total Tickets Sold</h5>
                        <h2>{{ $ticketsSold ?? 0 }}</h2>
                    </div>
                    
                    <div>
                        <h5>Total Revenue</h5>
                        <h2>${{ number_format($bookingStats->revenue ?? 0, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
