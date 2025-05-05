@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">User Details: {{ $user->name }}</h4>
                    <div>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Name</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created</th>
                                        <td>{{ $user->created_at->format('F j, Y, g:i a') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td>{{ $user->updated_at->format('F j, Y, g:i a') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Assigned Roles</h5>
                            </div>
                            <div class="card-body">
                                @if($user->roles->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Role</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->roles as $role)
                                                    <tr>
                                                        <td>{{ $role->name }}</td>
                                                        <td>{{ $role->description ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No roles assigned to this user.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Permissions (via Roles)</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $allPermissions = collect();
                            foreach($user->roles as $role) {
                                $allPermissions = $allPermissions->merge($role->permissions);
                            }
                            $allPermissions = $allPermissions->unique('id');
                        @endphp

                        @if($allPermissions->count() > 0)
                            <div class="row">
                                @foreach($allPermissions as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="badge bg-light text-dark p-2 d-flex align-items-center">
                                            <i class="mdi mdi-check-circle text-success me-2"></i>
                                            <span>{{ $permission->name }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No permissions granted to this user.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 