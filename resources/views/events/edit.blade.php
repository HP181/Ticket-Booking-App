@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Event</h1>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="card mb-4">
                    <div class="card-header">Event Details</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Event Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $event->location) }}" required>
                            @error('location')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="event_date" class="form-label">Event Date</label>
                                <input type="date" class="form-control @error('event_date') is-invalid @enderror" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
                                @error('event_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($event->start_time)->format('H:i')) }}" required>
                                @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($event->end_time)->format('H:i')) }}" required>
                                @error('end_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Event Image</label>
                            @if($event->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
                            <small class="form-text text-muted">Upload a new image only if you want to change the current one.</small>
                            @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('organizer.events') }}" class="btn btn-secondary">Back to Events</a>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Event Status</div>
                <div class="card-body">
                    <p>Current Status: 
                        @if($event->is_published)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Unpublished</span>
                        @endif
                    </p>
                    
                    <form action="{{ route('events.update-status', $event->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn {{ $event->is_published ? 'btn-warning' : 'btn-success' }} w-100">
                            {{ $event->is_published ? 'Unpublish Event' : 'Publish Event' }}
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">Ticket Types</div>
                <div class="card-body">
                    @if($ticketTypes->count() > 0)
                        <div class="list-group mb-3">
                            @foreach($ticketTypes as $ticket)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $ticket->name }}</h6>
                                            <p class="mb-1">${{ number_format($ticket->price, 2) }}</p>
                                            <small>Available: {{ $ticket->available_quantity }}/{{ $ticket->quantity }}</small>
                                        </div>
                                        <div>
                                            <a href="{{ route('ticket-types.edit', ['eventId' => $event->id, 'ticketTypeId' => $ticket->id]) }}" class="btn btn-sm btn-primary">Edit</a>
                                            
                                            <form action="{{ route('ticket-types.destroy', ['eventId' => $event->id, 'ticketTypeId' => $ticket->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ticket type?')">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No ticket types added yet.</p>
                    @endif
                    
                    <a href="{{ route('ticket-types.create', $event->id) }}" class="btn btn-primary w-100">Add Ticket Type</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header text-white bg-danger">Danger Zone</div>
                <div class="card-body">
                    <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <p>This action cannot be undone. This will permanently delete this event and all related data.</p>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">Delete Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection