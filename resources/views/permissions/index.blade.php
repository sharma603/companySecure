@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Permissions</h4>
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Add New Permission
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                            <tr>
                                <td>{{ $permission->name }}</td>
                                <td>{{ $permission->description }}</td>
                                <td>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-info btn-sm">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this permission?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No permissions found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 