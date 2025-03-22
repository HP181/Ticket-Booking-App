@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Bookings</h1>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Your Bookings</h5>
                </div>
                <div class="card-body">
                    @if($bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Date</th>
                                        <th>Ticket Type</th>
                                        <th>Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->event->title }}</td>
                                            <td>{{ $booking->event->event_date->format('M j, Y') }}</td>
                                            <td>{{ $booking->ticketType->name }}</td>
                                            <td>{{ $booking->quantity }}</td>
                                            <td>${{ number_format($booking->total_amount, 2) }}</td>
                                            <td>
                                                @if($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-primary">Details</a>
                                                    
                                                    @if($booking->status == 'pending')
                                                        <a href="{{ route('payment.create', $booking->id) }}" class="btn btn-sm btn-success">Pay</a>
                                                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</button>
                                                        </form>
                                                    @elseif($booking->status == 'confirmed' && $booking->event->event_date >= now()->startOfDay())
                                                        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                                        <a href="{{ route('bookings.cancel-confirmation', $booking->id) }}" class="btn btn-sm btn-danger">Cancel</a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->links() }}
                        </div>
                    @else
                        <p class="text-center">You don't have any bookings yet.</p>
                        <div class="text-center">
                            <a href="{{ route('events.index') }}" class="btn btn-primary">Browse Events</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('events.index') }}" class="btn btn-primary">Browse Events</a>
    </div>
</div>
@endsection