$(document).ready(function() {
    // Replace school-related functions with company ones
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    loadCompanies();
});

function loadCompanies() {
    $.ajax({
        url: '/admin/companies',
        type: 'GET',
        success: function(response) {
            // ... handle response
        }
    });
}