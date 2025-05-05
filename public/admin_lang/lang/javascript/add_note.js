// add_note.js

// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.addNoteModalInitialized = false;

$(document).ready(function() {
    console.log('add_note.js: Document ready');
    
    // Track form submission status
    var isSubmitting = false;

    // Simpler modal show handler - just add one field
    $('#addNoteModal').on('show.bs.modal', function() {
        console.log('Add Note modal opening');
        
        // Get company ID from URL if present
        var companyId = getCompanyIdFromUrl();
        console.log('Company ID from URL:', companyId);
        
        // Set the hidden company ID field
        $('#hidden_company_id').val(companyId);
        
        // Clear any existing fields
        $('#customFieldsContainer').empty();
        
        // Add just one field with a simple structure
        addBasicField();
    });
    
    // Simplified function to add a basic field (just label and value)
    function addBasicField() {
        $('#customFieldsContainer').append(`
            <div class="row g-2 align-items-center custom-field-row mb-2">
                <div class="col-4">
                    <input type="text" name="custom_labels[]" class="form-control custom-label-input" placeholder="Label" value="" required>
                </div>
                <div class="col-6">
                    <input type="text" name="custom_values[]" class="form-control custom-value-input" placeholder="Value" value="" required>
                </div>
                <div class="col-2 second-value-container" style="display: none">
                    <input type="text" name="custom_second_values[]" class="form-control custom-second-value-input" placeholder="Second Value" value="">
                </div>
                <div class="col-2 d-flex align-items-center">
                    <button type="button" class="btn btn-info btn-sm addSecondInputBtn" title="Add Second Input">
                        <i class="mdi mdi-plus"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm removeCustomFieldBtn ms-1" title="Remove Field">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
                <input type="hidden" name="custom_types[]" class="custom-type-select" value="text">
            </div>
        `);
    }
    
    // Function to get company ID from URL
    function getCompanyIdFromUrl() {
        // Check for company ID in URL path
        var path = window.location.pathname;
        var matches = path.match(/\/companies\/(\d+)/);
        if (matches && matches.length > 1) {
            return matches[1];
        }
        
        // Check for company ID in query string
        var params = new URLSearchParams(window.location.search);
        return params.get('company_id');
    }

    // Handle edit modal show event
    $('#editNoteModal').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var noteId = button.data('note-id');
        var form = $('#editNoteForm');
        
        // Clear previous fields
        $('#editCustomFieldsContainer').empty();
        
        // Set form action
        form.attr('action', `/notes/${noteId}`);
        
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
                    $('#edit_company_id').val(note.company_id).trigger('change');
                    
                    // Add fields
                    if (note.data && Array.isArray(note.data)) {
                        // Handle data as array
                        note.data.forEach(item => {
                            addCustomField(
                                item.label || '',
                                item.value || '',
                                item.secondValue || '',
                                item.type || 'custom',
                                '#editCustomFieldsContainer'
                            );
                        });
                    } else if (note.data) {
                        // Handle legacy data format (object with key-value pairs)
                        Object.entries(note.data).forEach(([label, field]) => {
                            var value = typeof field === 'object' ? field.value : field;
                            var secondValue = typeof field === 'object' ? field.secondValue || '' : '';
                            var type = typeof field === 'object' ? field.type || 'custom' : 'custom';
                            
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
            error: function(xhr) {
                console.error('Error fetching note:', xhr);
                alert('Error fetching note details. Please try again.');
            }
        });
    });

    // Main initialization when document is ready
    $(document).ready(function() {
        // Initialize Select2 for the company dropdown
        $('.company-select').select2();

        // Add a custom field when the add field button is clicked
        $('#addCustomFieldBtn').click(function() {
            addBasicField();
        });

        // Add a specific type of field when a type is selected
        $('#addFieldBtn').click(function() {
            addCustomField();
        });

        // Remove a custom field when the remove button is clicked
        $(document).on('click', '.removeCustomFieldBtn', function() {
            $(this).closest('.custom-field-row').remove();
        });
        
        // Handle the add second input button
        $(document).on('click', '.addSecondInputBtn', function() {
            let row = $(this).closest('.custom-field-row');
            let secondValueContainer = row.find('.second-value-container');
            
            if (secondValueContainer.is(':visible')) {
                secondValueContainer.hide();
                $(this).removeClass('btn-danger').addClass('btn-info');
                row.find('.col-6').removeClass('col-4').addClass('col-6');
            } else {
                secondValueContainer.show();
                $(this).removeClass('btn-info').addClass('btn-danger');
                row.find('.col-6').removeClass('col-6').addClass('col-4');
            }
        });

        // Handle form submission
        $('#noteForm').submit(function(e) {
            e.preventDefault();
            
            // Get the form and submit button
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var action = form.attr('action');
            
            // Disable submit button to prevent double submission
            submitButton.prop('disabled', true);
            
            // Get the company ID from the hidden field
            var companyId = $('#hidden_company_id').val() || $('[name="company_id"]').val();
            
            // If company ID is not found, try to get it from URL
            if (!companyId) {
                var pathMatch = window.location.pathname.match(/\/companies\/(\d+)\/dashboard/);
                if (pathMatch && pathMatch.length > 1) {
                    companyId = pathMatch[1];
                }
            }
            
            // Build data array from custom fields
            var customData = [];
            $('.custom-field-row').each(function() {
                var row = $(this);
                var type = row.find('.custom-type-select').val();
                var label = row.find('.custom-label-input').val();
                var value = row.find('.custom-value-input').val();
                var secondValueContainer = row.find('.second-value-container');
                var secondValue = '';
                
                // Only include second value if the container is visible
                if (secondValueContainer.is(':visible')) {
                    secondValue = row.find('.custom-second-value-input').val() || '';
                }
                
                if (label && value) {
                    customData.push({
                        type: type || 'text',
                        label: label,
                        value: value,
                        secondValue: secondValue
                    });
                }
            });
            
            // Prepare form data
            var formData = {
                company_id: companyId,
                data: customData
            };
            
            // Log the data being sent (for debugging)
            console.log('Submitting form data:', formData);
            
            // Send AJAX request
            $.ajax({
                url: action,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Form submitted successfully:', response);
                        
                        // Check if there's a redirect URL
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        handleFormError(response, submitButton);
                    }
                },
                error: function(xhr) {
                    handleFormError(xhr.responseJSON, submitButton);
                }
            });
            
            return false;
        });

        // Handle form submission for the edit form
        $('#editNoteForm').submit(function(e) {
            e.preventDefault();
            
            // Get the form and submit button
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');
            var action = form.attr('action');
            
            // Disable submit button to prevent double submission
            submitButton.prop('disabled', true);
            
            // Get the company ID from the dropdown
            var companyId = $('#edit_company_id').val();
            
            // Build data array from custom fields
            var customData = [];
            $('#editCustomFieldsContainer .custom-field-row').each(function() {
                var row = $(this);
                var type = row.find('.custom-type-select').val();
                var label = row.find('.custom-label-input').val();
                var value = row.find('.custom-value-input').val();
                var secondValueContainer = row.find('.second-value-container');
                var secondValue = '';
                
                // Only include second value if the container is visible
                if (secondValueContainer.is(':visible')) {
                    secondValue = row.find('.custom-second-value-input').val() || '';
                }
                
                if (label && value) {
                    customData.push({
                        type: type || 'custom',
                        label: label,
                        value: value,
                        secondValue: secondValue
                    });
                }
            });
            
            // Prepare form data
            var formData = {
                company_id: companyId,
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT',
                data: customData
            };
            
            // Log the data being sent (for debugging)
            console.log('Submitting edit form data:', formData);
            
            // Send AJAX request
            $.ajax({
                url: action,
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Form updated successfully:', response);
                        alert('Note updated successfully!');
                        $('#editNoteModal').modal('hide');
                        window.location.reload();
                    } else {
                        handleFormError(response, submitButton);
                    }
                },
                error: function(xhr) {
                    handleFormError(xhr.responseJSON, submitButton);
                }
            });
            
            return false;
        });
    });

    // Helper function to handle form errors
    function handleFormError(response, submitButton) {
        var errorMessage = 'Error saving note: ';
        
        if (response && response.errors) {
            errorMessage += '\n' + Object.entries(response.errors)
                .map(([field, messages]) => `${field}: ${messages.join(', ')}`)
                .join('\n');
                
            // Add validation styling for company if it's the error
            if (response.errors.company_id) {
                $('select[name="company_id"]').addClass('is-invalid');
            }
        } else if (response && response.message) {
            errorMessage += response.message;
        } else {
            errorMessage += 'An unknown error occurred';
        }
        
        alert(errorMessage);
        submitButton.prop('disabled', false);
        isSubmitting = false;
    }

    // Reset submission status when modals are hidden
    $('#addNoteModal, #editNoteModal').on('hidden.bs.modal', function() {
        var form = $(this).find('form');
        
        // Reset flags and state
        isSubmitting = false;
        form.find('button[type="submit"]').prop('disabled', false);
        
        // Clear validation styling
        form.find('.is-invalid').removeClass('is-invalid');
        
        // Reset form fields
        if ($(this).attr('id') === 'addNoteModal') {
            form[0].reset();
            $('#customFieldsContainer').empty();
            addCustomField();
        } else {
            $('#editCustomFieldsContainer').empty();
        }
    });
});

// Function to add custom field
function addCustomField(label = '', value = '', secondValue = '', type = 'text', container = '#editCustomFieldsContainer') {
    $(container).append(`
        <div class="row g-2 align-items-center custom-field-row mb-2">
            <div class="col-3">
                <select name="custom_types[]" class="form-select custom-type-select">
                    <option value="text" ${type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="number" ${type === 'number' ? 'selected' : ''}>Number</option>
                    <option value="date" ${type === 'date' ? 'selected' : ''}>Date</option>
                    <option value="email" ${type === 'email' ? 'selected' : ''}>Email</option>
                    <option value="url" ${type === 'url' ? 'selected' : ''}>URL</option>
                    <option value="custom" ${type === 'custom' ? 'selected' : ''}>Custom</option>
                </select>
            </div>
            <div class="col-3">
                <input type="text" name="custom_labels[]" class="form-control custom-label-input" placeholder="Label" value="${label}" required>
            </div>
            <div class="col-4">
                <input type="text" name="custom_values[]" class="form-control custom-value-input" placeholder="Value" value="${value}" required>
            </div>
            <div class="col-2 second-value-container" style="${secondValue ? 'display: block' : 'display: none'}">
                <input type="text" name="custom_second_values[]" class="form-control custom-second-value-input" placeholder="Second Value" value="${secondValue}">
            </div>
            <div class="col-2 d-flex align-items-center">
                <button type="button" class="btn btn-info btn-sm addSecondInputBtn" title="Add Second Input">
                    <i class="mdi ${secondValue ? 'mdi-minus' : 'mdi-plus'}"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm removeCustomFieldBtn ms-1" title="Remove Field">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `);
}

var proBanner = document.querySelector('#proBanner');
if (proBanner) {
    proBanner.classList.add('d-flex');
}