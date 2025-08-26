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
                $("#editAmountPawned").val(pawn.amount_pawned);
                $("#editNotes").val(pawn.notes);
                $("#editDatePawned").val(pawn.date_pawned);

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
$("#editPawnForm").on("submit", function(e) {
    e.preventDefault();

    $.ajax({
        url: "../processes/pawn_edit_process.php",
        type: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                Swal.fire("Success", response.message, "success");
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
});
