$(document).on("click", ".viewPawnBtn", function (e) {
    e.preventDefault();
    let pawnId = $(this).data("id");

    // Open modal first
    $("#viewPawnModal").modal("show");

    // Clear old table data before appending
    $("#tuboPaymentsTable tbody").empty();
    $("#partialPaymentsTable tbody").empty();

    // Fetch pawn details
    $.getJSON("../api/pawn_get.php", { pawn_id: pawnId }, function (res) {
        if (res.status === "success") {
            let pawn = res.pawn;

            // Fill basic pawn info
            $("#viewCustomerName").val(pawn.customer_name);
            $("#viewContactNo").val(pawn.contact_no);
            $("#viewAddress").val(pawn.address);
            $("#viewUnitDescription").val(pawn.unit_description);
            $("#viewCategory").val(pawn.category);
            $("#viewAmountPawned").val("₱" + parseFloat(pawn.amount_pawned).toFixed(2));
            $("#viewNotes").val(pawn.notes || "");
            $("#viewPassKey").val(pawn.pass_key || "");
            $("#viewDatePawned").val(pawn.date_pawned);
            $("#viewDueDate").val(pawn.current_due_date);
            $("#viewStatus").val(pawn.status);
            $("#viewInterest").val(pawn.interest_rate);


            if (pawn.photo_path) {
                $("#view_pawn_preview").attr("src", "../" + pawn.photo_path);
            } else {
                $("#view_pawn_preview").attr("src", "../assets/img/avatar.png");
            }

            // --- Populate Tubo Payments Table ---
            if (res.tubo_history && res.tubo_history.length > 0) {
                res.tubo_history.forEach((tubo, index) => {
                    $("#tuboPaymentsTable tbody").append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${tubo.date_paid}</td>
                            <td>${tubo.period_start} to ${tubo.period_end}</td>
                            <td>${tubo.months_covered} month(s)</td>
                            <td>${tubo.interest_rate ? tubo.interest_rate : '-'}</td>
                            <td>₱${parseFloat(tubo.interest_amount || 0).toFixed(2)}</td>
                        </tr>
                    `);
                });
            } else {
                $("#tuboPaymentsTable tbody").append(`
                    <tr><td colspan="5" class="text-center text-muted">No tubo payments found</td></tr>
                `);
            }

            // --- Populate Partial Payments Table ---
            if (res.partial_history && res.partial_history.length > 0) {
                res.partial_history.forEach((partial, index) => {
                    $("#partialPaymentsTable tbody").append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${partial.date_paid}</td>
                            <td>₱${parseFloat(partial.amount_paid || 0).toFixed(2)}</td>
                           
                            <td>₱${parseFloat(partial.remaining_principal || 0).toFixed(2)}</td>
                            <td>${partial.status || '-'}</td>
                        </tr>
                    `);
                });
            } else {
                $("#partialPaymentsTable tbody").append(`
                    <tr><td colspan="7" class="text-center text-muted">No partial payments found</td></tr>
                `);
            }

        } else {
            alert(res.message || "Error loading pawn details");
        }
    }).fail(function () {
        alert("Server error while fetching pawn details.");
    });
});
