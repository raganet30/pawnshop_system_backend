
    // partial payment function
    $(document).ready(function () {
        // Handle "Add Partial Payment" button click
        $(document).on("click", ".addPartialPaymentBtn", function () {
            let pawnId = $(this).data("id");

            $.ajax({
                url: "../api/pawn_get.php",
                method: "GET",
                data: { pawn_id: pawnId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        let pawn = response.pawn;
                        let interestRate = parseFloat(response.branch_interest) || 0.06;

                        // --- Populate histories ---
                        // Tubo history
                        let tuboRows = "";
                        if (response.tubo_history && response.tubo_history.length > 0) {
                            response.tubo_history.forEach(t => {
                                tuboRows += `
                                <tr>
                                    <td>${t.date_paid}</td>
                                    <td>${t.period_start}</td>
                                    <td>${t.period_end}</td>
                                    <td>₱${parseFloat(t.interest_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                </tr>
                            `;
                            });
                        } else {
                            tuboRows = "<tr><td colspan='4'>No tubo payments</td></tr>";
                        }
                        $("#ppTuboHistory tbody").html(tuboRows);

                        // Partial history
                        let partialRows = "";
                        if (response.partial_history && response.partial_history.length > 0) {
                            response.partial_history.forEach(p => {
                                partialRows += `
                                <tr>
                                    <td>${p.date_paid}</td>
                                    <td>₱${parseFloat(p.amount_paid).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                    <td>₱${parseFloat(p.remaining_principal).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                    <td>${p.notes || ""}</td>
                                </tr>
                            `;
                            });
                        } else {
                            partialRows = "<tr><td colspan='4'>No partial payments</td></tr>";
                        }
                        $("#ppPartialHistory tbody").html(partialRows);

                        // Compute months covered (default)
                        let datePawned = new Date(pawn.date_pawned);
                        let today = new Date();
                        let diffMonths =
                            (today.getFullYear() - datePawned.getFullYear()) * 12 +
                            (today.getMonth() - datePawned.getMonth());
                        if (today.getDate() > datePawned.getDate()) diffMonths++;
                        if (diffMonths < 1) diffMonths = 1;

                        // Fill modal fields
                        $("#ppPawnerName").val(pawn.customer_name);
                        $("#ppUnit").val(pawn.unit_description);
                        $("#ppCategory").val(pawn.category);
                        $("#ppDatePawned").val(pawn.date_pawned);
                        $("#ppAmountPawned").val("₱" + parseFloat(pawn.amount_pawned).toLocaleString());
                        $("#ppNotes").val(pawn.notes);
                        $("#ppMonths").val(diffMonths + " month(s)");

                        // Hidden fields
                        $("#ppPawnId").val(pawn.pawn_id);
                        $("#ppInterestRate").val(interestRate);
                        $("#ppPrincipal").val(pawn.amount_pawned);

                        // Reset
                        $("#ppAmount").val("");
                        $("#ppSummary").html("");

                        // Keep histories for live computation
                        $("#partialPaymentModal").data("tuboHistory", response.tubo_history || []);
                        $("#partialPaymentModal").data("partialHistory", response.partial_history || []);
                        $("#partialPaymentModal").data("pawnDate", pawn.date_pawned);

                        // Show modal
                        $("#partialPaymentModal").modal("show");
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("Failed to fetch pawn details.");
                }
            });
        });

        // Live computation when partial payment is entered
        $("#ppAmount").on("input", function () {
            let entered = parseFloat($(this).val()) || 0;
            let principal = parseFloat($("#ppPrincipal").val());
            let interestRate = parseFloat($("#ppInterestRate").val()) || 0.06;
            let tuboHistory = $("#partialPaymentModal").data("tuboHistory") || [];
            let partialHistory = $("#partialPaymentModal").data("partialHistory") || [];
            let pawnDate = new Date($("#partialPaymentModal").data("pawnDate"));
            let today = new Date();

            if (entered >= principal) {
                $("#ppSummary").html(`<span class="text-danger">Partial payment cannot exceed or equal to pawned amount!</span>`);
                return;
            }

            // --- Interest logic ---
            let startDate = pawnDate;
            let interest = 0;

            // Check tubo
            if (tuboHistory.length > 0) {
                let lastTubo = tuboHistory[0]; // DESC ordered
                let tuboDue = new Date(lastTubo.end_date);
                if (tuboDue >= today) {
                    interest = 0; // waived
                } else {
                    startDate = tuboDue;
                }
            }

            // Check partials
            if (partialHistory.length > 0) {
                let lastPartial = partialHistory[0];
                let partialDate = new Date(lastPartial.date_paid);

                let daysSincePartial = Math.floor((today - partialDate) / (1000 * 60 * 60 * 24));

                if (daysSincePartial < 31) {
                    // Waive interest for recent partial
                    interest = 0;
                    startDate = partialDate;
                } else if (partialDate > startDate) {
                    // Otherwise, move start date to partial
                    startDate = partialDate;
                }
            }

            // Only compute interest if not waived
            if (interest === 0 && tuboHistory.length === 0 && partialHistory.length === 0) {
                let diffMonths = Math.max(1, Math.ceil((today - startDate) / (1000 * 60 * 60 * 24 * 31)));
                interest = principal * interestRate * diffMonths;
            }


            let remaining = principal - entered;
            let totalPay = entered + interest;

            $("#ppSummary").html(`
            <div>Original Principal: ₱${principal.toLocaleString()}</div>
            <div>Partial Payment: ₱${entered.toLocaleString()}</div>
            <div>Remaining Principal: ₱${remaining.toLocaleString()}</div>
            <div>Interest: ₱${interest.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
            <hr>
            <strong>Total Payable Now: ₱${totalPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong>
        `);
        });

        // Handle form submit (save partial payment)
        $("#partialPaymentForm").on("submit", function (e) {
            e.preventDefault();

            let pawnId = $("#ppPawnId").val();
            let partialAmount = parseFloat($("#ppAmount").val()) || 0;
            let principal = parseFloat($("#ppPrincipal").val()) || 0;

            if (!pawnId || partialAmount <= 0) {
                Swal.fire("Invalid", "Please enter a valid partial payment amount.", "warning");
                return;
            }

            if (partialAmount > principal) {
                Swal.fire("Error", "Partial payment cannot exceed the current principal.", "error");
                return;
            }

            Swal.fire({
                title: "Confirm Partial Payment",
                html: `Save partial payment of ₱${partialAmount.toLocaleString()}?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, Save",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $("#partialPaymentForm").serialize();

                    $.ajax({
                        url: "../processes/save_partial_payment.php",
                        method: "POST",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            if (response.status === "success") {
                                $("#partialPaymentModal").modal("hide");

                                Swal.fire({
                                    title: "Success!",
                                    html: response.message,
                                    icon: "success"
                                });

                                $("#pawnTable").DataTable().ajax.reload();
                            } else {
                                Swal.fire("Error", response.message, "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error", "Failed to save partial payment.", "error");
                        }
                    });
                }
            });
        });
    });


