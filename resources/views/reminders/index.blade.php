@extends('template')

@section('contents')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">Reminders</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addReminderModal">
                        <i class="mdi mdi-plus"></i> Add New Reminder
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Company</th>
                                <th>Reminder Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reminders as $reminder)
                            <tr>
                                <td>{{ $reminder->title }}</td>
                                <td>{{ Str::limit($reminder->description, 50) }}</td>
                                <td>{{ $reminder->company->name ?? 'N/A' }}</td>
                                <td>{{ $reminder->reminder_date->format('M d, Y') }}</td>
                                <td>
                                    @if($reminder->is_completed)
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm edit-reminder" 
                                           data-reminder-id="{{ $reminder->id }}"
                                           data-bs-toggle="modal" 
                                           data-bs-target="#editReminderModal">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No reminders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Reminder Modal -->
<div class="modal fade" id="addReminderModal" tabindex="-1" aria-labelledby="addReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addReminderModalLabel">
                    <i class="mdi mdi-bell-plus me-1"></i> Add New Reminder
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addReminderForm" action="{{ route('reminders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="title" class="form-label font-weight-bold">Title</label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="Enter reminder title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="reminder_date" class="form-label font-weight-bold">Reminder Date</label>
                                <input type="date" class="form-control form-control-lg" id="reminder_date" name="reminder_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label font-weight-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter reminder details"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="reminder_email" class="form-label font-weight-bold">Email for Notification</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="mdi mdi-email-outline"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control" id="reminder_email" name="reminder_email" 
                                           value="{{ Auth::user()->email ?? '' }}" placeholder="Email to receive reminder">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="mdi mdi-information-outline"></i> 
                                    An email will be sent to this address on the reminder date
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="company_id" class="form-label font-weight-bold">Company (Optional)</label>
                                <select class="form-control" id="company_id" name="company_id">
                                    <option value="">No Company</option>
                                    @if(Auth::check() && Auth::user()->companies)
                                        @foreach(Auth::user()->companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No companies available. Please contact administrator.</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> Save Reminder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Reminder Modal -->
<div class="modal fade" id="editReminderModal" tabindex="-1" aria-labelledby="editReminderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editReminderModalLabel">
                    <i class="mdi mdi-bell-ring me-1"></i> Edit Reminder
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReminderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="edit_title" class="form-label font-weight-bold">Title</label>
                                <input type="text" class="form-control form-control-lg" id="edit_title" name="title" placeholder="Enter reminder title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="edit_reminder_date" class="form-label font-weight-bold">Reminder Date</label>
                                <input type="date" class="form-control form-control-lg" id="edit_reminder_date" name="reminder_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_description" class="form-label font-weight-bold">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" placeholder="Enter reminder details"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_reminder_email" class="form-label font-weight-bold">Email for Notification</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="mdi mdi-email-outline"></i>
                                        </span>
                                    </div>
                                    <input type="email" class="form-control" id="edit_reminder_email" name="reminder_email" placeholder="Email to receive reminder">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="mdi mdi-information-outline"></i> 
                                    An email will be sent to this address on the reminder date
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="edit_company_id" class="form-label font-weight-bold">Company (Optional)</label>
                                <select class="form-control" id="edit_company_id" name="company_id">
                                    <option value="">No Company</option>
                                    @if(Auth::check() && Auth::user()->companies)
                                        @foreach(Auth::user()->companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No companies available. Please contact administrator.</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group form-check mb-3 border-top pt-3 mt-2">
                        <input type="checkbox" class="form-check-input" id="edit_is_completed" name="is_completed" value="1">
                        <label class="form-check-label" for="edit_is_completed">Mark as Completed</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> Update Reminder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize date picker for better date selection
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#reminder_date, #edit_reminder_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            altInput: true,
            altFormat: "F j, Y at h:i K",
            minDate: "today"
        });
    } else {
        // Fallback to enhanced default date input
        $("#reminder_date, #edit_reminder_date").each(function() {
            // Set min date to today
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            var todayStr = yyyy + '-' + mm + '-' + dd;
            $(this).attr('min', todayStr);
        });
    }
    
    // Format dates for better display
    $('.reminder-date').each(function() {
        var date = new Date($(this).text());
        $(this).text(date.toLocaleDateString());
    });
    
    // Handle edit reminder button click
    $('.edit-reminder').click(function() {
        var reminderId = $(this).data('reminder-id');
        var form = $('#editReminderForm');
        
        // Reset form
        form[0].reset();
        
        // Set the form action URL
        form.attr('action', `/reminders/${reminderId}`);
        
        // Fetch reminder data and populate the form
        $.ajax({
            url: `/reminders/${reminderId}/edit`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response && response.reminder) {
                    var reminder = response.reminder;
                    $('#edit_title').val(reminder.title);
                    $('#edit_description').val(reminder.description);
                    $('#edit_company_id').val(reminder.company_id);
                    $('#edit_reminder_email').val(reminder.reminder_email || '');
                    
                    // Format the date properly for the date input (YYYY-MM-DD)
                    var reminderDate = new Date(reminder.reminder_date);
                    var formattedDate = reminderDate.toISOString().split('T')[0];
                    $('#edit_reminder_date').val(formattedDate);
                    
                    // Set the completed checkbox
                    $('#edit_is_completed').prop('checked', reminder.is_completed);
                }
            },
            error: function() {
                alert('Error loading reminder data. Please try again.');
            }
        });
    });
    
    // AJAX form submission for Add Reminder
    $('#addReminderForm').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        
        if (!validateForm(form)) {
            return false;
        }
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    $('#addReminderModal').modal('hide');
                    showSuccessAlert('Reminder created successfully!');
                    // Reload the page after a brief delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                handleFormErrors(xhr);
            }
        });
    });
    
    // AJAX form submission for Edit Reminder
    $('#editReminderForm').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        
        if (!validateForm(form)) {
            return false;
        }
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    $('#editReminderModal').modal('hide');
                    showSuccessAlert('Reminder updated successfully!');
                    // Reload the page after a brief delay
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                handleFormErrors(xhr);
            }
        });
    });
    
    // Helper functions
    function validateForm(form) {
        var isValid = true;
        
        // Basic validation
        form.find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showErrorAlert('Please fill all required fields');
        }
        
        return isValid;
    }
    
    function handleFormErrors(xhr) {
        var response = xhr.responseJSON;
        var errorMessage = 'An error occurred while saving the reminder.';
        
        if (response && response.errors) {
            // Handle validation errors
            var errorList = '';
            $.each(response.errors, function(field, errors) {
                // Add is-invalid class to the input
                $(`#${field}`).addClass('is-invalid');
                
                // Create error messages
                $.each(errors, function(index, error) {
                    errorList += `<li>${error}</li>`;
                });
            });
            
            if (errorList) {
                errorMessage = `<ul class="mb-0">${errorList}</ul>`;
            }
        } else if (response && response.message) {
            errorMessage = response.message;
        }
        
        showErrorAlert(errorMessage);
    }
    
    function showSuccessAlert(message) {
        // Implement your success notification here
        // This is just a basic example
        alert(message);
    }
    
    function showErrorAlert(message) {
        // Implement your error notification here
        // This is just a basic example
        alert(message);
    }
});
</script>
@endsection 