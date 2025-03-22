@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Organizer Dashboard</h1>
    
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Events</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalEvents }}</h2>
                    <p class="card-text">Events you've created</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Published Events</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $publishedEvents }}</h2>
                    <p class="card-text">Events visible to attendees</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Bookings</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalBookings }}</h2>
                    <p class="card-text">Bookings for your events</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total Revenue</div>
                <div class="card-body">
                    <h2 class="card-title">${{ number_format($totalRevenue, 2) }}</h2>
                    <p class="card-text">From confirmed bookings</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">Quick Actions</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('events.create') }}" class="btn btn-primary btn-lg d-block mb-2">Create New Event</a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('organizer.events') }}" class="btn btn-secondary btn-lg d-block mb-2">Manage Events</a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('events.index') }}" class="btn btn-info btn-lg d-block mb-2">View Public Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection