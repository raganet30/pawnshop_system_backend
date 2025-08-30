$(document).on("click", ".editPawnBtn", function(e) {
    e.preventDefault();
    let pawnId = $(this).data("id");

    $.ajax({
        url: "../api/pawn_get.php",
        type: "GET",
        data: { pawn_id: pawnId },
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                let pawn = response.pawn;

                // Fill modal fields
                $("#editPawnId").val(pawn.pawn_id);
                $("#editCustomerName").val(pawn.customer_name);
                $("#editContactNo").val(pawn.contact_no);
                $("#editAddress").val(pawn.address);
                $("#editUnitDescription").val(pawn.unit_description);
                $("#editCategory").val(pawn.category);
                $("#editNotes").val(pawn.notes);
                $("#editDatePawned").val(pawn.date_pawned);

                // Amount fields: hidden raw value + formatted visible value
                $("#editAmountPawned").val(pawn.amount_pawned);
                $("#editAmountPawnedVisible").val(
                    Number(pawn.amount_pawned).toLocaleString('en-PH', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    })
                );

                // Attach currency formatter (ensure your money_separator.js function is called only once)
                attachCurrencyFormatter(
                    document.getElementById('editAmountPawnedVisible'),
                    document.getElementById('editAmountPawned')
                );

                // Show modal
                $("#editPawnModal").modal("show");
            } else {
                Swal.fire("Error", response.error || "Could not fetch pawn data.", "error");
            }
        },
        error: function() {
            Swal.fire("Error", "Failed to fetch pawn data.", "error");
        }
    });
});

// Handle Edit Form submit
// Handle Edit Form submit with confirmation
$("#editPawnForm").on("submit", function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Save changes?',
        text: "Do you want to update this pawn's information?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../processes/pawn_edit_process.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $("#editPawnModal").modal("hide");
                        $("#pawnTable").DataTable().ajax.reload();
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                },
                error: function() {
                    Swal.fire("Error", "Something went wrong.", "error");
                }
            });
        }
    });
});
