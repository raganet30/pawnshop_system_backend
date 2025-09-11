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


    // Remove the original event listener that caused the issue
    // The code below is removed from your original file:
    // $("#partialPaymentModal").on("shown.bs.modal", function () {
    //     $("#ppAmount").focus();
    // });


    // Live computation when partial payment is entered
    // Helper: calculate months between two dates (partial month counts as full)
// --- Helpers ---
function parseYMD(ymd) {
    if (!ymd) return null;
    const parts = String(ymd).split("-").map(Number);
    if (parts.length !== 3) return null;

    // Build a "local date only" (no timezone shift)
    return new Date(parts[0], parts[1] - 1, parts[2], 12); 
    // 👆 Noon avoids timezone rollbacks
}


function monthsBetween(startDate, endDate) {
    if (!(startDate instanceof Date) || !(endDate instanceof Date)) return 0;
    // normalize time-of-day
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

// --- Core compute function (callable) ---
function computePartialSummary() {
    // Read inputs (safe parsing)
    const entered = parseFloat($("#ppAmount").val()) || 0;
    const principal = parseFloat($("#ppPrincipal").val()) || 0;
    const interestRate = parseFloat($("#ppInterestRate").val()) || 0.06;

    const tuboHistory = $("#partialPaymentModal").data("tuboHistory") || [];
    const partialHistory = $("#partialPaymentModal").data("partialHistory") || [];

    const currentDueDateRaw = $("#partialPaymentModal").data("currentDueDate"); // ex: "2025-11-11"
    const pawnDateRaw = $("#partialPaymentModal").data("pawnDate");
    const todayRaw = $("#ppDatePaid").val(); // payment date input (YYYY-MM-DD)

    const currentDueDate = parseYMD(currentDueDateRaw);
    const pawnDate = parseYMD(pawnDateRaw) || null;
    const todayLocal = parseYMD(todayRaw) || new Date();

    // Basic validations shown to user
    if (entered <= 0) {
        $("#ppSummary").html(`<span class="text-danger">Enter a valid partial amount!</span>`);
        $("#ppInterestDue").val("0.00");
        $("#ppTotalPayable").val("0.00");
        return;
    }
    if (entered >= principal) {
        $("#ppSummary").html(`<span class="text-danger">Partial payment cannot exceed or equal to remaining principal!</span>`);
        $("#ppInterestDue").val("0.00");
        $("#ppTotalPayable").val("0.00");
        return;
    }

    // Prepare vars
    let interest = 0;
    let waiveInterest = false;
    let startDate = null;

    // Get latest tubo coverage end (period_end or new_due_date depending on your data)
    // Try 'period_end' first (tubo period coverage) then 'new_due_date'
    let lastTuboEnd = findLatestDate(tuboHistory, 'period_end') || findLatestDate(tuboHistory, 'new_due_date');

    // Determine flags
    const hasTubo = !!lastTuboEnd;
    const hasPartial = (partialHistory && partialHistory.length > 0);

    // --- Step 2: If has tubo payments, check coverage
    if (hasTubo) {
        // if today is strictly before coverage end -> waive. If equal or after, compute.
        if (todayLocal <= lastTuboEnd) {
            waiveInterest = true;
            interest = 0;
        } else {
            // start exactly from tubo end date (you said you want start at tubo new_due_date)
            startDate = new Date(lastTuboEnd);
        }
    }
    // --- Step 3: If has partial payments (and no tubo) ---
    else if (hasPartial) {
        // Use current_due_date as base
        if (currentDueDate && todayLocal <= currentDueDate) {
            // if payment is strictly before due date -> waive
            waiveInterest = true;
            interest = 0;
        } else if (currentDueDate) {
            // start from current due date (exactly)
            startDate = new Date(currentDueDate);
        } else {
            // fallback if no current due date: start from pawnDate
            startDate = pawnDate || new Date();
        }
    }
    // --- No tubo & no partial (fallback) ---
    else {
        // compute from currentDueDate if available, otherwise pawn date
        if (currentDueDate && todayLocal >= currentDueDate) {
            startDate = new Date(currentDueDate);
        } else if (pawnDate) {
            startDate = new Date(pawnDate);
        } else {
            startDate = new Date();
        }
    }

    // --- Compute interest if we need to ---
    if (!waiveInterest && startDate) {
        const diffMonths = monthsBetween(startDate, todayLocal);
        interest = principal * interestRate * diffMonths;
    } else {
        interest = 0;
    }

    // Final values
    const remaining = principal - entered;
    const totalPay = entered + interest;

    // Update hidden inputs / summary
    $("#ppInterestDue").val(interest.toFixed(2));
    $("#ppTotalPayable").val(totalPay.toFixed(2));

    $("#ppSummary").html(`
        <div>Original Principal: ₱${principal.toLocaleString()}</div>
        <div>Partial Payment: ₱${entered.toLocaleString()}</div>
        <div>Remaining Principal: ₱${remaining.toLocaleString()}</div>
        <div>Interest: ₱${interest.toLocaleString(undefined, { minimumFractionDigits: 2 })}</div>
        <hr>
        <strong>Total Payable: ₱${totalPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}</strong>
    `);

    // Debug: quickly show details in console to verify
    // console.debug("computePartialSummary debug:", {
    //     today: todayLocal && todayLocal.toISOString().split("T")[0],
    //     currentDueDate: currentDueDate && currentDueDate.toISOString().split("T")[0],
    //     lastTuboEnd: lastTuboEnd && lastTuboEnd.toISOString().split("T")[0],
    //     startDate: startDate && startDate.toISOString().split("T")[0],
    //     hasTubo, hasPartial, waiveInterest,
    //     principal, interestRate, interest
    // });
}

// --- Bind handlers: amount input AND date change (so selecting date re-calculates) ---
$(document).off("input", "#ppAmount").on("input", "#ppAmount", computePartialSummary);
$(document).off("change", "#ppDatePaid").on("change", "#ppDatePaid", computePartialSummary);

// optional: run once when modal shows (if you set defaults)
$("#partialPaymentModal").on("shown.bs.modal", function () {
    computePartialSummary();
});



// --- Add this right after ---
    $("#ppDatePaid").on("change", function () {
        $("#ppAmount").trigger("input"); // retrigger the computation
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