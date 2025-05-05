$(document).ready(function () {
    $("#AddCompanyForm").submit(function (event) {
        event.preventDefault();
        
        // Get form data
        var formData = {
            name: $("#CompanyName").val(),
            contact: $("#CompanyContact").val(),
            address: $("#CompanyAddress").val(),
            url: $("#CompanyUrl").val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Send AJAX request
        $.ajax({
            url: '/companies',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Clear form
                    $("#AddCompanyForm")[0].reset();
                    
                    // Close modal
                    $('#staticBackdrop').modal('hide');
                    
                    // Refresh company list
                    getCompanies();
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong',
                    });
                }
            },
            error: function(xhr) {
                // Show error message
                let errorMessage = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            }
        });
    });
});