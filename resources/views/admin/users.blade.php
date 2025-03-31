@extends('layouts.app')

@section('content')
<div class="container">
    <h1>User Management</h1>
    
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role == 'organizer')
                                        <span class="badge bg-warning text-dark">Organizer</span>
                                    @else
                                        <span class="badge bg-info">Attendee</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M j, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit-role', $user->id) }}" class="btn btn-sm btn-primary">Edit Role</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4 ">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .pagination svg {
        width: 16px !important;
        height: 16px !important;
    }
</style>
@endsection