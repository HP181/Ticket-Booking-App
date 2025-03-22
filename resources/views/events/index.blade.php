@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">All Events</h1>
    
    <div class="row">
        @forelse($events as $event)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" class="card-img-top" alt="{{ $event->title }}">
                    @else
                        <div class="card-img-top bg-light text-center py-5">No Image</div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> {{ $event->event_date->format('F j, Y') }}<br>
                                <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}<br>
                                <i class="bi bi-geo-alt"></i> {{ $event->location }}
                            </small>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">No events found.</div>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection