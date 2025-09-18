// Helper: add months safely
function addMonths(dateStr, months) {
    const d = new Date(dateStr);
    d.setMonth(d.getMonth() + months);
    if (d.getDate() !== new Date(dateStr).getDate()) {
        d.setDate(0); // adjust for shorter months
    }
    return d.toISOString().split("T")[0];
}

// Format date nicely
function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric" });
}

// Build Tubo selector dynamically
function buildTuboSelector(startDate, maxMonths = 4) {
    const $selector = $("#tpMonthsSelector");
    $selector.empty();

    function addMonths(dateStr, months) {
        const d = new Date(dateStr);
        d.setMonth(d.getMonth() + months);
        if (d.getDate() !== new Date(dateStr).getDate()) {
            d.setDate(0); // adjust for shorter months
        }
        return d.toISOString().split("T")[0];
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString("en-US", { month: "short", day: "2-digit", year: "numeric" });
    }

    if (!startDate) return;

    for (let i = 1; i <= maxMonths; i++) {
        const periodStart = startDate;
        const periodEnd = addMonths(periodStart, i);

        const label = (i === 1)
            ? `1 month: ${formatDate(periodStart)} → ${formatDate(periodEnd)}`
            : `Advance ${i} months: ${formatDate(periodStart)} → ${formatDate(periodEnd)}`;

        $selector.append(
            $("<option>", {
                value: i,
                "data-start": periodStart,
                "data-end": periodEnd,
                text: label
            })
        );
    }
}



// Populate tubo history table
function populateTuboHistory(history) {
    const $tbody = $("#tpTuboHistory tbody");
    $tbody.empty();

    if (!history || history.length === 0) {
        $tbody.append(`<tr><td colspan="3" class="text-center text-muted">No tubo payments yet</td></tr>`);
        return;
    }

    history.forEach(row => {
        $tbody.append(`
            <tr>
                <td>${formatDate(row.date_paid)}</td>
                <td>${row.period_start} to ${row.period_end}</td>
                <td>${row.months_covered} month(s)</td>
                <td>₱${parseFloat(row.interest_amount).toFixed(2)}</td>
            </tr>
        `);
    });
}

// Populate partial payments table
function populatePartialHistory(history) {
    const $tbody = $("#tpPartialHistory tbody");
    $tbody.empty();

    if (!history || history.length === 0) {
        $tbody.append(`<tr><td colspan="4" class="text-center text-muted">No partial payments yet</td></tr>`);
        return;
    }

    history.forEach(row => {
        $tbody.append(`
            <tr>
                <td>${formatDate(row.date_paid)}</td>
                <td>₱${parseFloat(row.amount_paid).toFixed(2)}</td>
                <td>₱${parseFloat(row.remaining_principal).toFixed(2)}</td>
                <td>${row.notes || "-"}</td>
            </tr>
        `);
    });
}

// Open Tubo Payment Modal
$(document).on("click", ".addTuboPaymentBtn", function () {
    const pawnId = $(this).data("id");

    $.ajax({
        url: "../api/pawn_get.php",
        method: "GET",
        data: { pawn_id: pawnId },
        success: function (res) {
            if (res.status !== "success") {
                alert("Failed to fetch pawn details.");
                return;
            }

            const pawn = res.pawn;
            $("#tpPawnId").val(pawn.pawn_id);
            $("#tpPawnerName").val(pawn.customer_name);
            $("#tpAmountPawned").val("₱" + parseFloat(pawn.amount_pawned).toFixed(2));
            $("#tpDatePawned").val(pawn.date_pawned);
            $("#tpCategory").val(pawn.category);
            $("#tpUnit").val(pawn.unit_description);
            $("#tpDueDate").val(pawn.current_due_date);
            // original ampount pawned
            $("#tpOriginalAmountPawned").val("₱" + parseFloat(pawn.original_amount_pawned).toFixed(2));



            // Build selector based on current_due_date
            let tuboStartDate;

            // Priority 1: tubo history
            if (res.tubo_history && res.tubo_history.length > 0) {
                const lastTubo = res.tubo_history[res.tubo_history.length - 1];
                tuboStartDate = lastTubo.new_due_date;
            }
            // Priority 2: partial history (no tubo yet)
            else if (res.partial_history && res.partial_history.length > 0) {
                tuboStartDate = pawn.current_due_date;
            }
            // Priority 3: fresh pawn
            else {
                tuboStartDate = pawn.date_pawned;

            }

            buildTuboSelector(tuboStartDate, 4);


            // Default payment date = today
            $("#tpDatePaid").val(new Date().toISOString().split("T")[0]);

            // Reset preview fields
            $("#tpMonthsCovered").val("");
            $("#tpNewDueDate").val("");
            $("#tpInterestAmount").val("");

            // Populate histories
            populateTuboHistory(res.tubo_history);
            populatePartialHistory(res.partial_history);

            // Show modal
            $("#tuboPaymentModal").modal("show");

            // Handle selector change
            $("#tpMonthsSelector").off("change").on("change", function () {
                const selected = $(this).find("option:selected");
                const start = selected.data("start");
                const end = selected.data("end");
                const months = parseInt($(this).val(), 10);

                $("#tpMonthsCovered").val(`${formatDate(start)} → ${formatDate(end)}`);

                let newDueDate;

                //  Check if fresh pawn (no tubo payments & no partial payments)
                if ((!res.tubo_history || res.tubo_history.length === 0) &&
                    (!res.partial_history || res.partial_history.length === 0)) {
                    // For fresh pawn, base it on current_due_date
                    newDueDate = addMonths(pawn.current_due_date, months);
                } else {
                    // Otherwise, use selector end
                    newDueDate = end;
                }

                $("#tpNewDueDate").val(newDueDate);

                // Compute interest
                const principal = parseFloat(pawn.amount_pawned);
                const rate = parseFloat(pawn.interest_rate);
                const interestAmount = principal * rate * months;

                $("#tpInterestAmount").val("₱" + interestAmount.toFixed(2));
            });


            //  Force default to 1st option (1 month) and trigger computation
            $("#tpMonthsSelector").prop("selectedIndex", 0).trigger("change");

        },
        error: function () {
            alert("Error fetching pawn details.");
        }
    });




});




// submit tubo payment
$("#tuboPaymentForm").on("submit", function (e) {
    e.preventDefault();

    // SweetAlert confirmation
    Swal.fire({
        title: 'Confirm Tubo Payment',
        text: "Are you sure you want to save this Tubo Payment?",
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Save',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {

            const selected = $("#tpMonthsSelector option:selected");
            const period_start = selected.data("start"); // already in YYYY-MM-DD
            const period_end = selected.data("end");   // already in YYYY-MM-DD

            // Proceed to save
            let covered = $("#tpMonthsCovered").val().split(" → ");

            let formData = {
                pawn_id: $("#tpPawnId").val(),
                // branch_id: $("#tpBranchId").val(),
                payment_date: $("#tpDatePaid").val(),
                months_covered: $("#tpMonthsSelector").val(),
                period_start: period_start,   // use data attribute
                period_end: period_end,       // use data attribute
                interest_amount: parseFloat($("#tpInterestAmount").val().replace("₱", "")),
                new_due_date: $("#tpNewDueDate").val(),
                notes: $("#tpNotes").val()
              
            };


            $.ajax({
                url: "../processes/save_tubo_payments.php",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function (res) {
    if (res.status === "success") {
        Swal.fire("Saved!", res.message, "success");
        $("#tuboPaymentModal").modal("hide");

        //  Generate AR No. from pawn_id + datePaid
        let pawnId   = $("#tpPawnId").val();
        let datePaid = $("#tpDatePaid").val() || new Date().toISOString().slice(0, 10); // YYYY-MM-DD

        let d  = new Date(datePaid);
        let mm = String(d.getMonth() + 1).padStart(2, "0");
        let dd = String(d.getDate()).padStart(2, "0");
        let yy = String(d.getFullYear()).slice(-2);

        let receiptNo = pawnId.toString().padStart(3, "0") + "-" + mm + dd + yy;

        //  Build print query
        let queryParams = {
            receipt_no: receiptNo,
            customer_name: $("#tpPawnerName").val(),
            item: $("#tpUnit").val(),
            date_paid: datePaid,
            months_covered: $("#tpMonthsSelector").val(),
            period_start: $("#tpMonthsSelector option:selected").data("start"),
            period_end: $("#tpMonthsSelector option:selected").data("end"),
            interest_amount: parseFloat($("#tpInterestAmount").val().replace("₱", "")).toFixed(2),
            new_due_date: $("#tpNewDueDate").val(),
            notes: $("#tpNotes").val(),
            original_amount_pawned: parseFloat($("#tpOriginalAmountPawned").val().replace("₱", "").replace(/,/g, "")).toFixed(2)

        };

        let printUrl = "../processes/print_tubo_payment_ar.php?" + $.param(queryParams);

        //  Auto-open print window
        window.open(printUrl, "_blank", "width=800,height=600");

        // (Optional) refresh histories
        // refreshTuboHistory();
        // refreshPartialHistory();
    } else {
        Swal.fire("Error!", res.message, "error");
    }
}
,
                error: function (xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'An unexpected error occurred: ' + error,
                        'error'
                    );
                }
            });
        }
    });
});

