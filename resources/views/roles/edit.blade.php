@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Role: {{ $role->name }}</h4>
                <form class="forms-sample" action="{{ route('roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Role Name" value="{{ old('name', $role->name) }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Permissions</label>
                        <div class="row mt-3">
                            @foreach($permissions as $permission)
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="checkbox" class="form-check-input" name="permissions[]" value="{{ $permission->id }}" 
                                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || 
                                                   (old('permissions') === null && $role->permissions->contains($permission->id)) ? 'checked' : '' }}>
                                            {{ $permission->name }}
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 