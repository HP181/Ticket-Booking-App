@extends('layouts.app')

@section('content')
<div class="container">
    <h1>System Reports</h1>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Event Statistics</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Total Events</th>
                                <td>{{ $eventStats->total ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Published Events</th>
                                <td>{{ $eventStats->published ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Unpublished Events</th>
                                <td>{{ ($eventStats->total ?? 0) - ($eventStats->published ?? 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Booking Statistics</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Total Bookings</th>
                                <td>{{ $bookingStats->total ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Confirmed Bookings</th>
                                <td>{{ $bookingStats->confirmed ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Total Revenue</th>
                                <td>${{ number_format($bookingStats->revenue ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Date Range Report</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label for="report_type" class="form-label">Report Type</label>
                    <select class="form-select" id="report_type" name="report_type">
                        <option value="all" {{ request('report_type') == 'all' ? 'selected' : '' }}>All Statistics</option>
                        <option value="events" {{ request('report_type') == 'events' ? 'selected' : '' }}>Events Only</option>
                        <option value="bookings" {{ request('report_type') == 'bookings' ? 'selected' : '' }}>Bookings Only</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
    
    @if(isset($periodStats))
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Report Results: {{ request('start_date') }} to {{ request('end_date') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Events in this period</h6>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Events Created</th>
                                    <td>{{ $periodStats->events_created ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Events Happening</th>
                                    <td>{{ $periodStats->events_happening ?? 0 }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Bookings in this period</h6>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Bookings Made</th>
                                    <td>{{ $periodStats->bookings_created ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Revenue Generated</th>
                                    <td>${{ number_format($periodStats->revenue_generated ?? 0, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection