// On page load, fetch companies instead of schools
$(document).ready(function() {
    getCompanies();
});

function getCompanies() {
    $.ajax({
        url: '/companies',
        type: 'GET',
        success: function(response) {
            if (response.status === 'success') {
                var companies = response.data;
                var tableBody = $('.activity-report-table');
                tableBody.empty();

                companies.forEach(function(company, index) {
                    var row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${company.name}</td>
                            <td>${company.contact || '-'}</td>
                            <td>${company.address || '-'}</td>
                            <td>${company.url ? `<a href="${company.url}" target="_blank">${company.url}</a>` : '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info profile-company" data-id="${company.id}" title="Profile">
                                    <i class="mdi mdi-account-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-primary edit-company" data-id="${company.id}" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-company" data-id="${company.id}" title="Delete">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.append(row);
                });

                // Attach profile handler
                $('.profile-company').off('click').on('click', function() {
                    var companyId = $(this).data('id');
                    window.location.href = '/companies/' + companyId + '/dashboard';
                });

                // Attach delete handler
                $('.delete-company').off('click').on('click', function() {
                    var companyId = $(this).data('id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You will not be able to recover this company!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/companies/' + companyId,
                                type: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Deleted!', response.message, 'success');
                                        getCompanies();
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!', 'Failed to delete company', 'error');
                                }
                            });
                        }
                    });
                });

                // Attach edit handler
                $('.edit-company').off('click').on('click', function() {
                    var companyId = $(this).data('id');
                    // Fetch company details and show in modal
                    $.ajax({
                        url: '/companies/' + companyId,
                        type: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                var company = response.data;
                                // Fill modal fields
                                $('#CompanyName').val(company.name);
                                $('#CompanyContact').val(company.contact);
                                $('#CompanyAddress').val(company.address);
                                $('#CompanyUrl').val(company.url);
                                $('#AddCompanyForm').attr('data-edit-id', companyId);
                                $('#staticBackdrop').modal('show');
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Failed to fetch company details', 'error');
                        }
                    });
                });
            } else {
                console.error('Error fetching companies');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
        }
    });
}

// school name delete  
$(document).ready(function(){
    $(".deleteSchool").click(function () {
      var schoolid =  $(this).attr('school_id');
       
      $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });

        $.ajax({
            url: "/admin/school-delete",
            method: 'POST',
            data: {
                id: schoolid,

            },

            success: function (response) {
                if (response == 'delete') {
                    Swal.fire({
                        title:  'delete success',
                        text: "Thank you",
                        icon: "success"
                    });
                    $(".close-btn").click();

                    schoolReload();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                        footer: '<a href="#">Why do I have this issue?</a>'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    });

});


// school name edit 
$(document).ready(function () {
    $(".updateSchool").click(function(){
        var schoolEdit = $(this).attr('school_id');
        //alert(schoolEdit);
        var school_id = $(this).parents().find('.school_id').html();
        console.log(school_id);
        
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        }); 

       // console.log(schoolEdit);
           // return false
        $.ajax({
            url: "/admin/school-update",
            method: 'GET',
            data: {
                id: schoolEdit
            },
            success: function (response) {
                if (response === 'update') {
                    console.log(response);
                    schoolReload();
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                        footer: '<a href="#">Why do I have this issue?</a>'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', error);
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                    footer: '<a href="#">Why do I have this issue?</a>'
                });
            }
        });





    });
    
});

// Handle add/edit form submission
$(document).ready(function() {
    getCompanies();
    $('#AddCompanyForm').off('submit').on('submit', function(event) {
        event.preventDefault();
        var companyId = $(this).attr('data-edit-id');
        var formData = {
            name: $('#CompanyName').val(),
            contact: $('#CompanyContact').val(),
            address: $('#CompanyAddress').val(),
            url: $('#CompanyUrl').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        var ajaxType = companyId ? 'PUT' : 'POST';
        var ajaxUrl = companyId ? '/companies/' + companyId : '/companies';
        $.ajax({
            url: ajaxUrl,
            type: ajaxType,
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#AddCompanyForm')[0].reset();
                    $('#AddCompanyForm').removeAttr('data-edit-id');
                    $('#staticBackdrop').modal('hide');
                    getCompanies();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Something went wrong',
                    });
                }
            },
            error: function(xhr) {
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