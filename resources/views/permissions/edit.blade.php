@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Permission: {{ $permission->name }}</h4>
                <form class="forms-sample" action="{{ route('permissions.update', $permission) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Permission Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Permission Name" value="{{ old('name', $permission->name) }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $permission->description) }}</textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Update</button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 