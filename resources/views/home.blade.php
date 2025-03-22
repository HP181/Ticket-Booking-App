@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Featured Events</div>

                <div class="card-body">
                    <div class="row">
                        @forelse($events as $event)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($event->image)
                                    <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}">
                                    @else
                                        <div class="card-img-top bg-light text-center py-5">No Image</div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> {{ $event->event_date->format('F j, Y') }}<br>
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
                                <p class="text-center">No events found.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection