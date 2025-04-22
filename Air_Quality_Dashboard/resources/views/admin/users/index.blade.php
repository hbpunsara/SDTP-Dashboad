@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">User Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">User Management</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Admin Users
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus me-1"></i> Add New User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Name</th>
                            <th width="35%">Email</th>
                            <th width="15%">Role</th>
                            <th width="20%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_admin)
                                <span class="badge bg-primary">Administrator</span>
                                @else
                                <span class="badge bg-secondary">Regular User</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                @if($user->id != session('admin_id'))
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-secondary" disabled title="You cannot delete your own account">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            About User Management
        </div>
        <div class="card-body">
            <p>The User Management panel allows you to add, edit, and delete administrator accounts for the Air Quality Dashboard system.</p>
            
            <ul>
                <li><strong>Administrator</strong>: Users with full access to all features of the admin dashboard.</li>
                <li><strong>Regular User</strong>: Users with limited permissions (currently not implemented).</li>
            </ul>
            
            <div class="alert alert-warning mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i>
                <strong>Note:</strong> You cannot delete your own account while you are logged in. This prevents accidental loss of admin access.
            </div>
        </div>
    </div>
</div>
@endsection
