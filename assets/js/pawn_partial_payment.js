// partial payment function
$(document).ready(function () {
    // Handle Add Partial Payment button
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
                    let interestRate = parseFloat(pawn.interest_rate) || 0.06;

                    // --- Tubo history ---
                    let tuboRows = "";
                    if (response.tubo_history?.length) {
                        response.tubo_history.forEach(t => {
                            tuboRows += `
                                <tr>
                                    <td>${t.date_paid}</td>
                                    <td>${t.period_start} to ${t.period_end}</td>
                                    <td>₱${parseFloat(t.interest_amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                </tr>`;
                        });
                    } else {
                        tuboRows = "<tr><td colspan='4'>No tubo payments</td></tr>";
                    }
                    $("#ppTuboHistory tbody").html(tuboRows);

                    // --- Partial history ---
                    let partialRows = "";
                    if (response.partial_history?.length) {
                        response.partial_history.forEach(p => {
                            partialRows += `
                                <tr>
                                    <td>${p.date_paid}</td>
                                    <td>₱${parseFloat(p.amount_paid).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                    <td>₱${parseFloat(p.remaining_principal).toLocaleString(undefined, { minimumFractionDigits: 2 })}</td>
                                    <td>${p.notes || ""}</td>
                                </tr>`;
                        });
                    } else {
                        partialRows = "<tr><td colspan='4'>No partial payments</td></tr>";
                    }
                    $("#ppPartialHistory tbody").html(partialRows);

                    // --- Compute months covered ---
                    let datePawned = new Date(pawn.date_pawned);
                    let today = new Date();
                    let diffMonths =
                        (today.getFullYear() - datePawned.getFullYear()) * 12 +
                        (today.getMonth() - datePawned.getMonth());
                    if (today.getDate() > datePawned.getDate()) diffMonths++;
                    if (diffMonths < 1) diffMonths = 1;

                    // --- Fill modal fields ---
                    $("#ppPawnerName").val(pawn.customer_name);
                    $("#ppUnit").val(pawn.unit_description);
                    $("#ppCategory").val(pawn.category);
                    $("#ppDatePawned").val(pawn.date_pawned);
                    $("#ppAmountPawned").val("₱" + parseFloat(pawn.amount_pawned).toLocaleString());
                    $("#ppNotes").val(pawn.notes);
                    $("#ppMonths").val(diffMonths + " month(s)");

                    $("#ppPawnId").val(pawn.pawn_id);
                    $("#ppInterestRate").val(interestRate);
                    $("#ppPrincipal").val(pawn.amount_pawned);
                    $("#ppOriginalAmountPawned").val(pawn.original_amount_pawned);




                    $("#ppAmount").val("");
                    $("#ppSummary").html("");

                    $("#ppDatePaid").val(new Date().toISOString().split("T")[0]);
                    $("#ppDueDate").val(pawn.current_due_date);

                    // Save histories
                    $("#partialPaymentModal").data("tuboHistory", response.tubo_history || []);
                    $("#partialPaymentModal").data("partialHistory", response.partial_history || []);
                    $("#partialPaymentModal").data("pawnDate", pawn.date_pawned);
                    $("#partialPaymentModal").data("currentDueDate", pawn.current_due_date);

                    // Show modal
                    $("#partialPaymentModal").modal("show");

                    // --- CORRECTED: Add focus here with a slight delay ---
                    setTimeout(function () {
                        $("#ppAmount").focus();
                    }, 150); // A small delay ensures the modal is fully ready
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("Failed to fetch pawn details.");
            }
        });
    });

    // --- Helpers ---
    function parseYMD(ymd) {
        if (!ymd) return null;
        const parts = String(ymd).split("-").map(Number);
        if (parts.length !== 3) return null;
        return new Date(parts[0], parts[1] - 1, parts[2], 12);
    }

    function monthsBetween(startDate, endDate) {
        if (!(startDate instanceof Date) || !(endDate instanceof Date)) return 0;
        startDate = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
        endDate = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());
        let months = (endDate.getFullYear() - startDate.getFullYear()) * 12 +
            (endDate.getMonth() - startDate.getMonth());
        if (endDate.getDate() > startDate.getDate()) months++;
        if (months < 1 && endDate >= startDate) months = 1;
        return months;
    }

    function findLatestDate(arr, key) {
        if (!Array.isArray(arr) || arr.length === 0) return null;
        let latest = null;
        for (const r of arr) {
            if (!r || !r[key]) continue;
            const d = parseYMD(r[key]);
            if (!d) continue;
            if (!latest || d.getTime() > latest.getTime()) latest = d;
        }
        return latest;
    }

    // --- Core compute function ---
    function computePartialSummary() {
        const entered = parseFloat($("#ppAmount").val()) || 0;
        const principal = parseFloat($("#ppPrincipal").val()) || 0;
        const interestRate = parseFloat($("#ppInterestRate").val()) || 0.06;

        const tuboHistory = $("#partialPaymentModal").data("tuboHistory") || [];
        const partialHistory = $("#partialPaymentModal").data("partialHistory") || [];

        const currentDueDateRaw = $("#partialPaymentModal").data("currentDueDate");
        const pawnDateRaw = $("#partialPaymentModal").data("pawnDate");
        const todayRaw = $("#ppDatePaid").val();

        const currentDueDate = parseYMD(currentDueDateRaw);
        const pawnDate = parseYMD(pawnDateRaw) || null;
        const todayLocal = parseYMD(todayRaw) || new Date();

        if (entered <= 0) {
            $("#ppSummary").html(`<span class="text-danger">Enter a valid partial amount!</span>`);
            $("#ppInterestDue").val("0.00");
            $("#ppTotalPayable").val("0.00");
            $("#ppBackendType").val(""); // indicate no backend
            return;
        }
        if (entered >= principal) {
            $("#ppSummary").html(`<span class="text-danger">Partial payment cannot exceed or equal to remaining principal!</span>`);
            $("#ppInterestDue").val("0.00");
            $("#ppTotalPayable").val("0.00");
            $("#ppBackendType").val("");
            return;
        }

        let interest = 0;
        let waiveInterest = false;
        let startDate = null;

        let lastTuboEnd = findLatestDate(tuboHistory, 'period_end') || findLatestDate(tuboHistory, 'new_due_date');
        const hasTubo = !!lastTuboEnd;
        const hasPartial = (partialHistory && partialHistory.length > 0);

        if (hasTubo) {
            if (todayLocal <= lastTuboEnd) {
                waiveInterest = true;
                interest = 0;
            } else {
                startDate = new Date(lastTuboEnd);
            }
        } else if (hasPartial) {
            if (currentDueDate && todayLocal <= currentDueDate) {
                waiveInterest = true;
                interest = 0;
            } else if (currentDueDate) {
                startDate = new Date(currentDueDate);
            } else {
                startDate = pawnDate || new Date();
            }
        } else {
            startDate = pawnDate || currentDueDate || new Date();
        }

        if (!waiveInterest && startDate) {
            const diffMonths = monthsBetween(startDate, todayLocal);
            interest = principal * interestRate * diffMonths;
        } else {
            interest = 0;
        }

        const remaining = principal - entered;
        const totalPay = entered + interest;

        $("#ppInterestDue").val(interest.toFixed(2));
        $("#ppTotalPayable").val(totalPay.toFixed(2));

        // Determine which backend to use
        $("#ppBackendType").val(interest > 0 ? "with_tubo" : "partial_only");

        $("#ppSummary").html(`
        <div>Principal: ₱${principal.toLocaleString()}</div>
        <div>Partial Payment: ₱${entered.toLocaleString()}</div>
        <div>Remaining Principal: ₱${remaining.toLocaleString()}</div>
        <div>Interest: ₱${interest.toLocaleString(undefined, { minimumFractionDigits: 2 })} 
            ${waiveInterest ? "<span class='text-success'>(Interest waived)</span>" : ""}
        </div>
        <div>Months: ${monthsBetween(startDate, todayLocal)} month/s</div>
        <hr>
        <strong>Total Payable: ₱${totalPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong>
    `);

        const monthsCovered = monthsBetween(startDate, todayLocal);
        let periodStart = startDate ? new Date(startDate) : new Date(todayLocal);
        let periodEnd = new Date(periodStart);
        periodEnd.setMonth(periodEnd.getMonth() + monthsCovered);
        let newDueDate = new Date(periodEnd);
        newDueDate.setMonth(newDueDate.getMonth() + 1);

        $("#ppPeriodStart").val(periodStart.toISOString().split("T")[0]);
        $("#ppPeriodEnd").val(periodEnd.toISOString().split("T")[0]);
        $("#ppMonthsCovered").val(monthsCovered);
        $("#ppNewDueDate").val(newDueDate.toISOString().split("T")[0]);
        $("#ppInterestAmount").val(interest.toFixed(2));

        console.debug("computePartialSummary debug:", {
            today: todayLocal && todayLocal.toISOString().split("T")[0],
            currentDueDate: currentDueDate && currentDueDate.toISOString().split("T")[0],
            lastTuboEnd: lastTuboEnd && lastTuboEnd.toISOString().split("T")[0],
            startDate: startDate && startDate.toISOString().split("T")[0],
            hasTubo, hasPartial, waiveInterest,
            principal, interestRate, interest,
            monthsDiff: monthsBetween(startDate, todayLocal)
        });
    }


    $(document).off("input", "#ppAmount").on("input", "#ppAmount", computePartialSummary);
    $(document).off("change", "#ppDatePaid").on("change", "#ppDatePaid", computePartialSummary);

    $("#partialPaymentModal").on("shown.bs.modal", function () {
        computePartialSummary();
    });

    $("#ppDatePaid").on("change", function () {
        $("#ppAmount").trigger("input");
    });

    $("#partialPaymentForm").on("submit", function (e) {
        e.preventDefault();
        let pawnId = $("#ppPawnId").val();
        let partialAmount = parseFloat($("#ppAmount").val()) || 0;
        let principal = parseFloat($("#ppPrincipal").val()) || 0;
        let interestDue = parseFloat($("#ppInterestDue").val()) || 0; // computed tubo
        let datePaid = $("#ppDatePaid").val();

        if (!pawnId || partialAmount <= 0) {
            Swal.fire("Invalid", "Please enter a valid partial payment amount.", "warning");
            return;
        }

        if (partialAmount > principal) {
            Swal.fire("Error", "Partial payment cannot exceed the current principal.", "error");
            return;
        }

        Swal.fire({
            title: "Confirm Payment",
            html: `Save partial payment of ₱${partialAmount.toLocaleString()}${interestDue > 0 ? " + tubo ₱" + interestDue.toLocaleString() : ""}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = $("#partialPaymentForm").serialize();

                // Decide backend
                let backendUrl = interestDue > 0
                    ? "../processes/save_partial_with_tubo.php"
                    : "../processes/save_partial_only.php";

                $.ajax({
                    url: backendUrl,
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

                            // Reload table
                            $("#pawnTable").DataTable().ajax.reload();

                            //  Generate receipt no. in JS
                            let pawnId = $("#ppPawnId").val();
                            let datePaid = $("#ppDatePaid").val() || new Date().toISOString().slice(0, 10); // YYYY-MM-DD

                            let d = new Date(datePaid);
                            let mm = String(d.getMonth() + 1).padStart(2, "0");
                            let dd = String(d.getDate()).padStart(2, "0");
                            let yy = String(d.getFullYear()).slice(-2);

                            let receiptNo = pawnId.toString().padStart(3, "0") + "-" + mm + dd + yy;

                            //  Build print URL
                            let queryParams = {
                                receipt_no: receiptNo, // now using JS-generated value
                                customer_name: $("#ppPawnerName").val(),
                                item: $("#ppUnit").val(),
                                date_paid: datePaid,
                                partial_amount: partialAmount.toFixed(2),
                                remaining_balance: (principal - partialAmount).toFixed(2),
                                original_amount_pawned: parseFloat($("#ppOriginalAmountPawned").val().replace("₱", "").replace(/,/g, "")) || 0

                            };

                            // Include tubo if paid
                            // Include tubo if paid
                            if (interestDue > 0) {
                                queryParams.tubo_amount = interestDue.toFixed(2);

                                // match backend variable names
                                queryParams.covered_from = $("#ppPeriodStart").val() || "";
                                queryParams.covered_to = $("#ppPeriodEnd").val() || "";
                            }




                            let printUrl = "../processes/print_tubo_partial_ar.php?" + $.param(queryParams);

                            //  Open new window for printing
                            window.open(printUrl, "_blank", "width=800,height=600");
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
