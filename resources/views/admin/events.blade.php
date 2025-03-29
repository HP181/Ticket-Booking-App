@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Event Management</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Organizer</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->id }}</td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ $event->user->name }}</td>
                                    <td>{{ $event->event_date->format('M j, Y') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>
                                        @if($event->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-secondary">Unpublished</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-info">View</a>
                                        
                                        <form action="{{ route('admin.events.update-status', $event->id) }}" method="POST" class="d-inline">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" value="{{ $event->is_published ? 0 : 1 }}">
    @if($event->is_published)
        <button type="submit" class="btn btn-sm btn-warning">Unpublish</button>
    @else
        <button type="submit" class="btn btn-sm btn-success">Publish</button>
    @endif
</form>
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
                <p class="text-center">No events found.</p>
            @endif
        </div>
    </div>
</div>
@endsection