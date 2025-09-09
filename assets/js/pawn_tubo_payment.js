
    // Utility function to add months to a date
    function addMonths(date, months) {
        let d = new Date(date);
        d.setMonth(d.getMonth() + months);
        return d.toISOString().slice(0, 10); // format YYYY-MM-DD
    }

    // Update default tubo values (period & interest)
    function updateTuboDefaults(p, res) {
        let amountPawned = parseFloat(p.amount_pawned);
        let interestRate = parseFloat(res.branch_interest); // use as-is

        let lastTubo = res.tubo_history[0] || null;
        let lastPartial = res.partial_history[0] || null;

        let startDate;

        if (lastTubo) {
            startDate = lastTubo.period_end;
        } else if (lastPartial) {
            startDate = lastPartial.date_paid;
        } else {
            startDate = p.date_pawned;
        }

        let months = parseInt($("#tpMonthsSelector").val()) || 1;
        let endDate = addMonths(startDate, months);

        // Compute end of current covered period
        let periodEnd = addMonths(startDate, months);


        // Compute new due date = periodEnd + 1 month
        let newDueDate = addMonths(periodEnd, 1);

        $("#tpMonthsCovered").val(`${startDate} to ${endDate}`);
        $("#tpInterestAmount").val((amountPawned * interestRate * months).toFixed(2));
        $("#tpNewDueDate").val(newDueDate);

        $("#tuboPaymentModal").data("period_start", startDate);
    }


    // Load pawn, tubo, and partial history, then show modal
    $(document).on("click", ".addTuboPaymentBtn", function (e) {
        e.preventDefault();
        let pawnId = $(this).data("id");

        $.get("../api/pawn_get.php", { pawn_id: pawnId }, function (res) {
            if (res.status === "success") {
                let p = res.pawn;

                // Fill pawn details
                $("#tpPawnId").val(p.pawn_id);
                $("#tpBranchId").val(p.branch_id);
                $("#tpPawnerName").val(p.customer_name);
                $("#tpCategory").val(p.category);
                $("#tpUnit").val(p.unit_description);
                $("#tpDatePawned").val(p.date_pawned);
                $("#tpAmountPawned").val(parseFloat(p.amount_pawned).toFixed(2));

                //  Set default Payment Date to today
                let today = new Date().toISOString().slice(0, 10);
                $("#tpDatePaid").val(today);


                // Store branch interest for computation
                $("#tuboPaymentModal").data("branch_interest", res.branch_interest);

                // Populate tubo history
                let tuboRows = "";
                res.tubo_history.forEach(h => {
                    tuboRows += `
                    <tr>
                        <td>${h.date_paid}</td>
                        <td>${h.period_start} to ${h.period_end}</td>
                        <td>${parseFloat(h.interest_amount).toFixed(2)}</td>
                    </tr>`;
                });
                $("#tpTuboHistory tbody").html(tuboRows);

                // Populate partial history
                let partialRows = "";
                res.partial_history.forEach(h => {
                    partialRows += `
                    <tr>
                        <td>${h.date_paid}</td>
                        <td>${parseFloat(h.amount_paid).toFixed(2)}</td>
                        <td>${parseFloat(h.remaining_principal).toFixed(2)}</td>
                        <td>${h.notes || ""}</td>
                    </tr>`;
                });
                $("#tpPartialHistory tbody").html(partialRows);

                // Initialize default tubo period & interest
                updateTuboDefaults(p, res);


                // Show modal
                $("#tuboPaymentModal").modal("show");
            } else {
                alert(res.message);
            }
        }, "json");
    });

    $("#tpMonthsSelector").on("change", function () {
        let months = parseInt($(this).val());

        // Get clean numeric pawn amount
        let amountPawned = Number($("#tpAmountPawned").val().replace(/,/g, ''));

        // Branch interest (already decimal, e.g., 0.06)
        let interestRate = parseFloat($("#tuboPaymentModal").data("branch_interest"));

        // Get start date
        let startDate = $("#tuboPaymentModal").data("period_start");

        // Compute end date of current covered period
        let endDate = addMonths(startDate, months);

        // Update Months Covered Period
        $("#tpMonthsCovered").val(`${startDate} to ${endDate}`);

        // Compute interest amount
        let interestAmount = amountPawned * interestRate * months;
        $("#tpInterestAmount").val(interestAmount.toFixed(2));

        // Compute new due date = end of current period + 1 month
        let newDueDate = addMonths(endDate, 1);
        $("#tpNewDueDate").val(newDueDate);

    });

    // Optional: Update period start if you want it to follow Payment Date instead
    $("#tpDatePaid").on("change", function () {
        // let selectedDate = $(this).val();
        // Optional logic to adjust period start if needed
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
            confirmButtonText: 'Yes, Save',
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed to save
                let formData = {
                    pawn_id: $("#tpPawnId").val(),
                    branch_id: $("#tpBranchId").val(),
                    payment_date: $("#tpDatePaid").val(),
                    months_covered: $("#tpMonthsSelector").val(),
                    period_start: $("#tpMonthsCovered").val().split(" to ")[0],
                    period_end: $("#tpMonthsCovered").val().split(" to ")[1],
                    interest_amount: $("#tpInterestAmount").val(),
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
                            Swal.fire(
                                'Saved!',
                                'Tubo payment has been saved successfully.',
                                'success'
                            );
                            $("#tuboPaymentModal").modal("hide");

                            // Refresh histories
                            // refreshTuboHistory();
                            // refreshPartialHistory();
                        } else {
                            Swal.fire(
                                'Error!',
                                res.message,
                                'error'
                            );
                        }
                    },
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
