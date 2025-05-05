$(document).ready(function() {
    $("#loginForm").submit(function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        
        // Set loading state
        submitButton.html('<span class="spinner-border spinner-border-sm me-2"></span> Signing in...');
        submitButton.prop('disabled', true);
        
        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    // Handle errors
                    if (response.errors) {
                        // Clear previous errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();
                        
                        // Display new errors
                        $.each(response.errors, function(field, errors) {
                            var input = $('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.after('<span class="invalid-feedback" role="alert"><strong>' + errors + '</strong></span>');
                        });
                    }
                }
            },
            error: function(xhr) {
                // Handle server errors
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                // Reset button state
                submitButton.html('<i class="fas fa-sign-in-alt me-2"></i> Sign In');
                submitButton.prop('disabled', false);
            }
        });
    });
});