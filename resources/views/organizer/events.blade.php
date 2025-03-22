@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Events</h1>
        <a href="{{ route('events.create') }}" class="btn btn-primary">Create New Event</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Tickets</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->event_date->format('M j, Y') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>
                                        @if($event->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-secondary">Unpublished</span>
                                        @endif
                                    </td>
                                    <td>{{ $event->ticketTypes->sum('available_quantity') ?? 0 }}/{{ $event->ticketTypes->sum('quantity') ?? 0 }}</td>
                                    <td>{{ $event->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="{{ route('organizer.event.bookings', $event->id) }}" class="btn btn-sm btn-info">Bookings</a>
                                            <a href="{{ route('organizer.event.report', $event->id) }}" class="btn btn-sm btn-secondary">Report</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <h4>You haven't created any events yet</h4>
                    <p>Get started by creating your first event!</p>
                    <a href="{{ route('events.create') }}" class="btn btn-primary mt-2">Create New Event</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection