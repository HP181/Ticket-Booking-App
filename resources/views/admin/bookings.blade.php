@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Booking Management</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Booking Ref</th>
                                <th>User</th>
                                <th>Event</th>
                                <th>Ticket Type</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->booking_reference }}</td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->event->title }}</td>
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
                                    <td>{{ $booking->created_at->format('M j, Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->links() }}
                </div>
            @else
                <p class="text-center">No bookings found.</p>
            @endif
        </div>
    </div>
</div>
@endsection