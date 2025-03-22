@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Users</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalUsers }}</h2>
                    <p class="card-text">Registered users in the system</p>
                    <a href="{{ route('admin.users') }}" class="btn btn-light">Manage Users</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Events</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalEvents }}</h2>
                    <p class="card-text">Events created in the system</p>
                    <a href="{{ route('admin.events') }}" class="btn btn-light">Manage Events</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Bookings</div>
                <div class="card-body">
                    <h2 class="card-title">{{ $totalBookings }}</h2>
                    <p class="card-text">Bookings made by users</p>
                    <a href="{{ route('admin.bookings') }}" class="btn btn-light">View Bookings</a>
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
                        <div class="col-md-6">
                            <a href="{{ route('admin.reports') }}" class="btn btn-primary btn-lg d-block mb-2">Generate Reports</a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('events.index') }}" class="btn btn-secondary btn-lg d-block mb-2">View Public Events</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection