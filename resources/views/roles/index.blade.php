@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Roles Management</h4>
                    <div>
                        <a href="{{ route('users.create.sub') }}" class="btn btn-success btn-sm me-2">
                            <i class="mdi mdi-account-plus"></i> Create Sub-User
                        </a>
                        <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="mdi mdi-account-plus"></i> Add New User
                        </button>
                        <a href="{{ route('roles.create') }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-plus"></i> Add New Role
                        </a>
                    </div>
                </div>
                
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Users</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                        <span class="badge bg-info text-white">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $role->users->count() + $role->registers->count() }}</td>
                                <td>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-info btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this role?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No roles found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <i class="mdi mdi-close"></i>
                </button>
            </div>
            <form id="addUserForm" method="POST" action="{{ route('users.modal.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="model_type">User Model</label>
                        <select class="form-select" id="model_type" name="model_type">
                            <option value="App\Models\Register" {{ $modelType == 'App\Models\Register' ? 'selected' : '' }}>Register</option>
                            <option value="App\Models\User" {{ $modelType == 'App\Models\User' ? 'selected' : '' }}>User</option>
                        </select>
                        <small class="form-text text-muted">Select the user model type</small>
                    </div>
                    <div class="form-group mb-3">
                        <label for="roles">Assign Roles</label>
                        <select class="form-select" id="roles" name="roles[]" multiple>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple roles</small>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Form submission handling with AJAX
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        title: 'Success!',
                        text: 'User created successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Reload the page to reflect changes
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = 'Failed to create user. Please check the form and try again.';
                    
                    if (errors) {
                        errorMessage = '<ul>';
                        $.each(errors, function(key, value) {
                            errorMessage += '<li>' + value + '</li>';
                        });
                        errorMessage += '</ul>';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        html: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    });
</script>
@endsection
