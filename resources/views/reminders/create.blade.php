@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Add New Reminder</h4>
                    <a href="{{ route('reminders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <form action="{{ route('reminders.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title" class="font-weight-bold">Title</label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title" 
                                    value="{{ old('title') }}" placeholder="Enter reminder title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="reminder_date" class="font-weight-bold">Reminder Date</label>
                                <input type="date" class="form-control form-control-lg" id="reminder_date" 
                                    name="reminder_date" value="{{ old('reminder_date', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-3">
                        <label for="description" class="font-weight-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                            rows="4" placeholder="Enter reminder details">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reminder_email" class="font-weight-bold">Email for Notification</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="mdi mdi-email-outline"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control" id="reminder_email" name="reminder_email" 
                                        value="{{ old('reminder_email', Auth::user()->email ?? '') }}" 
                                        placeholder="Email to receive reminder">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="mdi mdi-information-outline"></i> 
                                    An email will be sent to this address on the reminder date
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_id" class="font-weight-bold">Company (Optional)</label>
                                <select class="form-control" id="company_id" name="company_id">
                                    <option value="">No Company</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group form-check mt-4 border-top pt-3">
                        <input type="checkbox" class="form-check-input" id="is_completed" 
                            name="is_completed" value="1" {{ old('is_completed') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_completed">Mark as Completed</label>
                    </div>
                    
                    <div class="mt-4 pt-2 text-right">
                        <a href="{{ route('reminders.index') }}" class="btn btn-light mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="mdi mdi-content-save"></i> Save Reminder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 