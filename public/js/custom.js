
function DeleteRecord(url, table_id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "DELETE",
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                success: function (response) {
                    // console.log(response);
                    if (response.success) {
                        $('#' + table_id).DataTable().ajax.reload();
                        Swal.fire(
                            'Done!',
                            response.message,
                            'success'
                        )
                    } else {
                        Swal.fire(
                            'Oops!',
                            response.message,
                            'error'
                        )
                    }
                }
            });
        }
    })
}
function updateStatus(element, id, table, url) {
    if($(element).prop('checked') == true)
    {
    var status = 1;
    }
    else
    {
    var status = 0;
    }

    var fd = new FormData();
    fd.append('status', status);
    fd.append('id', id);
    fd.append('table', table);
    $.ajax({
        type: "POST",
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: fd,
        url: url,
        success: function (response) {
            if (response.success) {
                Swal.fire(
                    'Done!',
                    response.message,
                    'success'
                )
            } else {
                Swal.fire(
                    'Oops!',
                    response.message,
                    'error'
                )
            }
        }
    });
}


