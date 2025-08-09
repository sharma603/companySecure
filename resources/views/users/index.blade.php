@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Sub-Users</h4>
                    <div>
                        @if(!empty($isAdmin) && $isAdmin)
                            <a href="{{ route('roles.index') }}" class="btn btn-info btn-sm me-2">
                                <i class="mdi mdi-shield-account"></i> Manage Roles
                            </a>
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-account-plus"></i> Add New User
                            </a>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-info">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" title="View details" data-bs-toggle="modal" data-bs-target="#userModal-{{ $user->id }}">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    @if(!empty($isAdmin) && $isAdmin)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($users as $user)
<!-- User Details Modal -->
<div class="modal fade" id="userModal-{{ $user->id }}" tabindex="-1" aria-labelledby="userModalLabel-{{ $user->id }}" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel-{{ $user->id }}">User Details - {{ $user->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Basic Info</h6>
            <table class="table table-sm">
              <tr><th>Name</th><td>{{ $user->name }}</td></tr>
              <tr><th>Email</th><td>{{ $user->email }}</td></tr>
              <tr><th>Created</th><td>{{ $user->created_at->format('M d, Y') }}</td></tr>
            </table>
            <h6 class="mt-3">Roles</h6>
            @forelse($user->roles as $role)
              <span class="badge bg-info me-1">{{ $role->name }}</span>
            @empty
              <p class="text-muted mb-0">No roles assigned</p>
            @endforelse
          </div>
          <div class="col-md-6">
            <h6>Permissions</h6>
            <div class="row g-2">
              @php
                $permViaRoles = collect();
                foreach ($user->roles as $r) { $permViaRoles = $permViaRoles->merge($r->permissions); }
                $permViaRoles = $permViaRoles->unique('id');
                $directPerms = (\Illuminate\Support\Facades\Schema::hasTable('permission_user') && method_exists($user, 'directPermissions'))
                    ? $user->directPermissions
                    : collect();
                $allPerms = $permViaRoles->merge($directPerms)->unique('id');
              @endphp
              @forelse($allPerms as $perm)
                <div class="col-md-6">
                  <div class="border rounded p-2 d-flex align-items-center">
                    <i class="mdi mdi-check-circle text-success me-2"></i>
                    <span>{{ $perm->name }}</span>
                  </div>
                </div>
              @empty
                <p class="text-muted">No permissions</p>
              @endforelse
            </div>
          </div>
        </div>

        @if(!empty($isAdmin) && $isAdmin)
        <hr/>
        <h6>Access Control</h6>
        <div class="alert alert-secondary py-2">
          <small>Use the actions below to grant/remove access for this user.</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-pencil"></i> Edit Roles
          </a>
          <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="mdi mdi-delete"></i> Delete User
            </button>
          </form>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach
@endsection 