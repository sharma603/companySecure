@extends('template')

@section('contents')
<!-- First script: Direct dropdown initializer that runs as soon as possible -->
<script>
// This script runs immediately to initialize elements
document.addEventListener('DOMContentLoaded', function() {
    console.log('EARLY INIT: DOM loaded');
    
    // Wait 100ms to ensure the DOM is stable
    setTimeout(function() {
        console.log('EARLY INIT: Checking for dropdown');
        
        // Check for the company dropdown
        var dropdown = document.getElementById('add_note_company_id');
        console.log('EARLY INIT: Company dropdown found:', dropdown ? 'Yes' : 'No');
        
        // If not found, try to locate it by other means
        if (!dropdown) {
            console.log('EARLY INIT: Trying alternative methods to find dropdown');
            
            // Try by name
            var dropdowns = document.getElementsByName('company_id');
            console.log('EARLY INIT: Dropdowns found by name:', dropdowns.length);
            
            if (dropdowns.length > 0) {
                // Set an ID on the first dropdown found
                dropdowns[0].id = 'add_note_company_id';
                console.log('EARLY INIT: Set ID on dropdown');
            }
            
            // Try by attribute
            var dataDropdowns = document.querySelectorAll('[data-company-dropdown="true"]');
            console.log('EARLY INIT: Dropdowns found by data attribute:', dataDropdowns.length);
            
            if (dataDropdowns.length > 0) {
                dataDropdowns[0].id = 'add_note_company_id';
                console.log('EARLY INIT: Set ID on data dropdown');
            }
        }
    }, 100);
});
</script>

<div class="container mt-4">
    <!-- Check if companies exist -->
    @if($companies->isEmpty())
    <div class="alert alert-warning">
        <strong>No companies found!</strong> You need to <a href="{{ route('add-company') }}">add a company</a> before creating notes.
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">All Notes</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal" 
                        @if($companies->isEmpty()) disabled title="Add a company first" @endif
                        id="addNoteBtn" onclick="prepareAddNoteModal(event)">
                        <i class="mdi mdi-note-plus"></i> Add New Note
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Fields</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notes as $note)
                                    <tr>
                                        <td>{{ $note->company->name ?? 'N/A' }}</td>
                                        <td>
                                            @if(is_array($note->data))
                                                @foreach($note->data as $field)
                                                    <div class="mb-1">
                                                        @if(isset($field['label']))
                                                            <strong>{{ $field['label'] }}:</strong>
                                                            {{ $field['value'] ?? '' }}
                                                            @if(isset($field['secondValue']) && !empty($field['secondValue']))
                                                                / {{ $field['secondValue'] }}
                                                            @endif
                                                        @else
                                                            @foreach($field as $key => $value)
                                                                @if($key !== 'type' && $key !== 'secondValue')
                                                                    <strong>{{ $key }}:</strong>
                                                                    {{ is_array($value) ? $value['value'] : $value }}
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $note->user->name }}</td>
                                        <td>{{ $note->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info edit-note" 
                                                    data-note-id="{{ $note->id }}"
                                                    data-company-id="{{ $note->company_id }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editNoteModal">
                                                <i class="mdi mdi-pencil"></i>
                                            </button>
                                            <form action="{{ route('notes.destroy', $note->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this note?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No notes found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNoteModalLabel">Add New Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('notes.store') }}" id="noteForm">
                    @csrf
                    <!-- Hidden company field - will be set by JavaScript -->
                    <input type="hidden" name="company_id" id="hidden_company_id">
                    
                    <div class="modal-body">
                        <div id="customFieldsContainer">
                            <!-- Initial field will be added by script -->
                        </div>
                        <button type="button" class="btn btn-info mt-3" id="addCustomFieldBtn">
                            <i class="mdi mdi-plus"></i> Add Field
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-outline-secondary" id="resetFieldsBtn">
                            <i class="mdi mdi-refresh"></i> Reset Fields
                        </button>
                        <button type="submit" class="btn btn-primary">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Note Modal -->
    <div class="modal fade" id="editNoteModal" tabindex="-1" aria-labelledby="editNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNoteModalLabel">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="editNoteForm" action="{{ route('notes.update', '') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_company_id" class="form-label required">Company <span class="text-danger">*</span></label>
                            <select class="form-select" name="company_id" id="edit_company_id" required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="editCustomFieldsContainer">
                            <!-- Dynamic fields will be added here -->
                        </div>
                        <button type="button" class="btn btn-info mt-3" id="editAddCustomFieldBtn">
                            <i class="mdi mdi-plus"></i> Add Field
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- Make sure jQuery is loaded first -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Check if jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        console.log('jQuery is loaded, version:', jQuery.fn.jquery);
        
        // Check company select
        var addNoteSelect = document.getElementById('add_note_company_id');
        console.log('Company select element (DOM):', addNoteSelect ? 'Found' : 'Not found');
        
        if (addNoteSelect) {
            console.log('Company options count:', addNoteSelect.options.length);
            var options = [];
            for (var i = 0; i < addNoteSelect.options.length; i++) {
                options.push({
                    value: addNoteSelect.options[i].value,
                    text: addNoteSelect.options[i].text
                });
            }
            console.log('Company options:', options);
        }
        
        // jQuery version of the check
        console.log('jQuery company select:', $('#add_note_company_id').length ? 'Found' : 'Not found');
        console.log('jQuery company options:', $('#add_note_company_id option').length);
    } else {
        console.error('jQuery is not loaded!');
    }
});
</script>

<!-- Main script file -->
<script src="{{ asset('admin_lang/lang/javascript/add_note.js') }}"></script>

<!-- Specific script for edit functionality -->
<script>
$(document).ready(function() {
    console.log('Document ready fired');
    console.log('jQuery version:', $.fn.jquery);
    console.log('Initial company select check:', $('#add_note_company_id').length);
    console.log('Initial company options:', $('#add_note_company_id option').length);
    
    // Handle edit button click
    $('.edit-note').click(function() {
        var noteId = $(this).data('note-id');
        var form = $('#editNoteForm');
        var baseUrl = "{{ route('notes.update', '') }}";
        
        // Set form action URL
        form.attr('action', baseUrl + '/' + noteId);
        
        // Clear previous fields
        $('#editCustomFieldsContainer').empty();
        
        // Fetch note data
        $.ajax({
            url: `/notes/${noteId}`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Note data received:', response);
                if (response.success) {
                    var note = response.note;
                    
                    // Set company ID
                    console.log('Setting company ID:', note.company_id);
                    $('#edit_company_id').val(note.company_id);
                    
                    // Verify company ID was set
                    setTimeout(function() {
                        var selectedCompany = $('#edit_company_id').val();
                        console.log('Verified company ID:', selectedCompany);
                        if (!selectedCompany) {
                            console.error('Company ID not set properly');
                        }
                    }, 100);
                    
                    // Add fields
                    if (note.data && Array.isArray(note.data)) {
                        note.data.forEach(field => {
                            addCustomField(
                                field.label || '',
                                field.value || '',
                                field.secondValue || '',
                                field.type || 'custom',
                                '#editCustomFieldsContainer'
                            );
                        });
                    } else if (note.data) {
                        // Legacy format handling
                        Object.entries(note.data).forEach(([label, field]) => {
                            var value = typeof field === 'object' ? field.value : field;
                            var secondValue = typeof field === 'object' ? field.secondValue || '' : '';
                            var type = typeof field === 'object' ? field.type || 'custom' : 'custom';
                            
                            console.log('Adding field:', {label, value, secondValue, type});
                            
                            addCustomField(
                                label,
                                value,
                                secondValue,
                                type,
                                '#editCustomFieldsContainer'
                            );
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching note:', {xhr, status, error});
                alert('Error fetching note details. Please try again.');
            }
        });
    });

    // Add field button for edit modal
    $('#editAddCustomFieldBtn').click(function() {
        addCustomField('', '', '', 'custom', '#editCustomFieldsContainer');
    });
});
</script>

<!-- Direct script to initialize the note modal without depending on external JS -->
<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded in page script');
    
    // Check if we're in a company dashboard by looking for company ID in URL
    function getCompanyIdFromUrl() {
        var path = window.location.pathname;
        var matches = path.match(/\/companies\/(\d+)\/dashboard/);
        if (matches && matches.length > 1) {
            return matches[1];
        }
        
        // Try to get from query string
        var urlParams = new URLSearchParams(window.location.search);
        var companyId = urlParams.get('company_id');
        if (companyId) {
            return companyId;
        }
        
        return null;
    }
    
    // Auto-select company in dropdown if we're in a company dashboard
    var currentCompanyId = getCompanyIdFromUrl();
    console.log('Current company ID from URL:', currentCompanyId);
    
    // Prepare function to set company ID when modal is opened
    window.setCompanyInModal = function() {
        if (currentCompanyId) {
            console.log('Setting company ID in modal:', currentCompanyId);
            var companyDropdown = document.getElementById('add_note_company_id');
            if (companyDropdown) {
                companyDropdown.value = currentCompanyId;
                console.log('Company dropdown value set to:', companyDropdown.value);
                
                // Disable the dropdown since we're in a specific company context
                companyDropdown.disabled = true;
                
                // Add a note indicating it's pre-selected
                var noteElement = document.createElement('div');
                noteElement.className = 'text-info small mt-1';
                noteElement.innerHTML = 'Company automatically selected from dashboard';
                companyDropdown.parentNode.appendChild(noteElement);
            } else {
                console.error('Company dropdown not found for auto-selection');
            }
        }
    };
    
    // Handle the reset fields button
    document.getElementById('resetFieldsBtn').addEventListener('click', function() {
        console.log('Reset fields button clicked');
        
        // Clear the container
        var container = document.getElementById('customFieldsContainer');
        if (container) {
            console.log('Clearing container');
            container.innerHTML = '';
            
            // Add a fresh field
            if (typeof addCustomField === 'function') {
                console.log('Adding fresh field');
                addCustomField('', '', '', 'custom', '#customFieldsContainer');
            } else {
                console.error('addCustomField function not available');
                // Fallback implementation if function isn't available
                container.innerHTML = `
                    <div class="row g-2 align-items-center custom-field-row mb-2">
                        <div class="col-3">
                            <select class="form-select custom-type-select" name="custom_types[]">
                                <option value="custom" selected>Custom</option>
                                <option value="email">Email</option>
                                <option value="gmail">Gmail</option>
                                <option value="outlook">Outlook</option>
                                <option value="github">GitHub</option>
                                <option value="windows">Windows Password</option>
                                <option value="number">Number</option>
                                <option value="text">Plain Text</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="text" name="custom_labels[]" class="form-control custom-label-input" placeholder="Label" value="" required>
                        </div>
                        <div class="col-3">
                            <input type="text" name="custom_values[]" class="form-control custom-value-input" placeholder="Value" value="" required>
                        </div>
                        <div class="col-2 second-value-container" style="display: none">
                            <input type="text" name="custom_second_values[]" class="form-control custom-second-value-input" placeholder="Second Value" value="">
                        </div>
                        <div class="col-1 d-flex align-items-center">
                            <button type="button" class="btn btn-info addSecondInputBtn" title="Add Second Input">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        } else {
            console.error('Container not found');
        }
    });

    // Also add a button click handler for initial field if needed
    document.getElementById('addNoteModal').addEventListener('show.bs.modal', function() {
        console.log('Modal shown - direct event handler');
        
        // Set the company ID if we have it
        window.setCompanyInModal();
        
        // Check if container is empty
        var container = document.getElementById('customFieldsContainer');
        if (container && container.children.length === 0) {
            console.log('Container is empty, adding initial field');
            
            // Trigger the reset button click to add a field
            document.getElementById('resetFieldsBtn').click();
        }
    });
});
</script>

<!-- Fallback script to fix any issues with the company dropdown - this must be the last script -->
<script>
// Last resort fix for the company dropdown issue
window.addEventListener('load', function() {
    // Wait for everything to be fully loaded
    setTimeout(function() {
        console.log('Final check for company dropdown');
        
        // Check all modals and fix any issues
        var addModal = document.getElementById('addNoteModal');
        var addForm = document.getElementById('noteForm');
        var addCompanyDropdown = document.getElementById('add_note_company_id');
        
        console.log('Modal found:', addModal ? 'Yes' : 'No');
        console.log('Form found:', addForm ? 'Yes' : 'No');
        console.log('Company dropdown found:', addCompanyDropdown ? 'Yes' : 'No');
        
        // If the dropdown isn't found, try to create it
        if (addForm && !addCompanyDropdown) {
            console.warn('Company dropdown not found, attempting to create it');
            
            // Find the first modal body to insert into
            var modalBody = addForm.querySelector('.modal-body');
            if (modalBody) {
                console.log('Modal body found, inserting dropdown');
                
                // Create the company dropdown
                var companyContainer = document.createElement('div');
                companyContainer.className = 'mb-3';
                companyContainer.innerHTML = `
                    <label for="add_note_company_id" class="form-label required">Company <span class="text-danger">*</span></label>
                    <select class="form-select" name="company_id" id="add_note_company_id" required style="display: block !important; visibility: visible !important;">
                        <option value="">Select Company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        Please select a company
                    </div>
                    <div class="small text-muted mt-1">
                        Total companies: {{ $companies->count() }}
                        @if($companies->count() > 0)
                            (First: {{ $companies->first()->name }})
                        @endif
                    </div>
                `;
                
                // Insert at the beginning of the modal body
                var firstChild = modalBody.firstChild;
                modalBody.insertBefore(companyContainer, firstChild);
                
                console.log('Company dropdown recreated');
            }
        }
        
        // Also check if the custom fields container exists, if not create it
        var customFieldsContainer = document.getElementById('customFieldsContainer');
        if (addForm && !customFieldsContainer) {
            console.warn('Custom fields container not found, attempting to create it');
            
            var modalBody = addForm.querySelector('.modal-body');
            if (modalBody) {
                console.log('Creating custom fields container');
                
                var container = document.createElement('div');
                container.id = 'customFieldsContainer';
                
                modalBody.appendChild(container);
                
                console.log('Container created, adding initial field');
                
                // Try to add an initial field
                if (typeof addCustomField === 'function') {
                    addCustomField('', '', '', 'custom', '#customFieldsContainer');
                }
            }
        }
    }, 500); // Wait half a second after page load
});
</script>

<script>
// Function to ensure the modal is ready before showing
function prepareAddNoteModal(event) {
    console.log('Add Note button clicked');
    // Check if companies exist
    var companyCount = {{ $companies->count() }};
    if (companyCount === 0) {
        console.warn('No companies available');
        alert('Please add a company first before creating notes.');
        event.preventDefault();
        return false;
    }
    
    // Ensure the company dropdown exists
    var dropdown = document.getElementById('add_note_company_id');
    if (!dropdown) {
        console.warn('Company dropdown not found, trying to fix');
        
        // Try to find by name
        var dropdowns = document.getElementsByName('company_id');
        if (dropdowns.length > 0) {
            dropdowns[0].id = 'add_note_company_id';
            console.log('Set ID on company dropdown');
            dropdown = dropdowns[0];
        } else {
            console.error('No company dropdown found');
            // We'll continue and let Bootstrap show the modal
            // The other scripts will attempt to fix this
        }
    } else {
        console.log('Company dropdown found, ID:', dropdown.id);
    }
    
    // Ensure the customFieldsContainer exists
    var container = document.getElementById('customFieldsContainer');
    if (!container) {
        console.warn('customFieldsContainer not found');
        
        // Try to find the modal body
        var modalBody = document.querySelector('#addNoteModal .modal-body');
        if (modalBody) {
            // Create the container
            container = document.createElement('div');
            container.id = 'customFieldsContainer';
            modalBody.appendChild(container);
            console.log('Created customFieldsContainer');
        }
    } else {
        console.log('customFieldsContainer found');
    }
    return true;
}
</script>
@endsection