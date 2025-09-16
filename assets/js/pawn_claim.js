$(function () {

    // Utility: Fill input fields from a mapping
    function fillFields(mapping, data) {
        for (const selector in mapping) {
            $(selector).val(data[mapping[selector]] ?? '');
        }
    }

    // Claim Button Click
    $(function () {

        // Utility: Fill input fields from a mapping
        function fillFields(mapping, data) {
            for (const selector in mapping) {
                $(selector).val(data[mapping[selector]] ?? '');
            }
        }

        // Claim Button Click
        $(document).on("click", ".claimPawnBtn", function (e) {
            e.preventDefault();
            const pawnId = $(this).data("id");

            $.getJSON("../api/pawn_get.php", { pawn_id: pawnId })
                .done((data) => {
                    if (data.status !== "success") {
                        return Swal.fire("Error", data.message || "Unable to fetch pawn details.", "error");
                    }

                    const pawn = data.pawn;
                    const principal = parseFloat(pawn.amount_pawned);
                    const interestRate = parseFloat(pawn.interest_rate) || 0.06; // decimal
                    const today = new Date();

                    // --- Determine default interest based on tubo and partial payments ---
                    let totalInterest = 0;

                    // Populate Tubo Payments History
                    let tuboRows = "";
                    if (data.tubo_history && data.tubo_history.length > 0) {
                        data.tubo_history.forEach((t, i) => {
                            tuboRows += `<tr>
                <td>${i + 1}</td>
                <td>${t.date_paid}</td>
                <td>${t.period_start} to ${t.period_end}</td>
                <td>${t.months_covered} month(s)</td>
                <td>${parseFloat(t.interest_rate).toFixed(2)}</td>
                <td>${parseFloat(t.interest_amount).toFixed(2)}</td>
            </tr>`;
                        });
                    }
                    $("#tuboPaymentsTable tbody").html(tuboRows);

                    // Populate Partial Payments History
                    let partialRows = "";
                    if (data.partial_history && data.partial_history.length > 0) {
                        data.partial_history.forEach((p, i) => {
                            partialRows += `<tr>
                <td>${i + 1}</td>
                <td>${p.date_paid}</td>
                <td>${parseFloat(p.amount_paid).toFixed(2)}</td>
                <td>${parseFloat(p.remaining_principal).toFixed(2)}</td>
                <td>${p.status}</td>
            </tr>`;
                        });
                    }
                    $("#partialPaymentsTable tbody").html(partialRows);

                    // ---------------- Interest Computation ----------------
                   // --- 1. Compute Claim Interest (global function) ---
function computeClaimInterest() {
    function parseYMD(ymd) {
        if (!ymd) return null;
        const parts = String(ymd).split("-").map(Number);
        return new Date(parts[0], parts[1] - 1, parts[2]);
    }
    function formatDateLocal(date) {
        return date ? date.toLocaleDateString("en-CA") : null;
    }

    const hasPartial = (pawn.has_partial_payments == 1) ||
        (Array.isArray(data.partial_history) && data.partial_history.length > 0);
    const hasTubo = (pawn.has_tubo_payments == 1) ||
        (Array.isArray(data.tubo_history) && data.tubo_history.length > 0);

    let claimDateStr = $("#claimDate").val();
    let todayLocal = claimDateStr ? parseYMD(claimDateStr) : new Date();
    if (!todayLocal || isNaN(todayLocal.getTime())) todayLocal = new Date();

    let startDate = parseYMD(pawn.date_pawned) || new Date();
    if (!startDate || isNaN(startDate.getTime())) startDate = new Date();

    let claimdiffMonths = 0;
    let totalInterest = 0;
    let waiveInterest = false;

    const interestOption = $("#interestOption").val(); // auto, waive, custom
    const principal = parseFloat(pawn.amount_pawned) || 0;
    const interestRate = parseFloat(pawn.interest_rate) || 0.06;

    if (interestOption === "waive") {
        waiveInterest = true;
        totalInterest = 0;
        claimdiffMonths = 0;
    } else if (interestOption === "custom") {
        totalInterest = parseFloat($("#customInterest").val()) || 0;
        claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
            (todayLocal.getMonth() - startDate.getMonth());
        if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
        if (claimdiffMonths < 1) claimdiffMonths = 1;
        waiveInterest = false;
    } else {
        // --- Auto Compute ---
        if (!hasPartial && !hasTubo && pawn.status === 'pawned') {
            claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                (todayLocal.getMonth() - startDate.getMonth());
            if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
            if (claimdiffMonths < 1) claimdiffMonths = 1;
            totalInterest = principal * interestRate * claimdiffMonths;
        } else if (hasTubo && pawn.status === 'pawned') {
            let lastTuboEnd = null;
            if (Array.isArray(data.tubo_history) && data.tubo_history.length > 0) {
                const lastTubo = data.tubo_history.reduce((a, b) => {
                    return parseYMD(a.period_end) > parseYMD(b.period_end) ? a : b;
                });
                lastTuboEnd = parseYMD(lastTubo.period_end);
                lastTuboEnd.setHours(23, 59, 59, 999);
            }

            todayLocal.setHours(0, 0, 0, 0);

            if (lastTuboEnd) {
                if (todayLocal <= lastTuboEnd) {
                    waiveInterest = true;
                    claimdiffMonths = 0;
                    totalInterest = 0;
                } else {
                    startDate = new Date(lastTuboEnd);
                    startDate.setHours(0, 0, 0, 0);
                    claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                        (todayLocal.getMonth() - startDate.getMonth());
                    if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                    if (claimdiffMonths < 1) claimdiffMonths = 1;
                    totalInterest = principal * interestRate * claimdiffMonths;
                }
            }
        }
    }

    // --- Update UI ---
    if (waiveInterest) {
        $("#claimMonths").val("Interest Waived");
        $("#claimInterest").val("₱0.00");
        $("#claimTotal").val("₱" + principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $("#claimMonthsValue").val(0);
    } else {
        $("#claimMonths").val(claimdiffMonths + " month(s)");
        $("#claimInterest").val("₱" + totalInterest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $("#claimTotal").val("₱" + (principal + totalInterest).toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $("#claimMonthsValue").val(claimdiffMonths);
    }

    // Hidden fields
    $("#claimPrincipalValue").val(principal.toFixed(2));
    $("#claimInterestValue").val(totalInterest.toFixed(2));
    $("#claimTotalValue").val((principal + totalInterest).toFixed(2));
    $("#claimPenalty").val('0.00');
}

// --- 2. Interest Option Change ---
$("#interestOption").on("change", function() {
    const option = $(this).val();

    if (option === "waive") {
    $("#customInterestWrapper").hide();
    $("#customInterest").val("");

    const principal = parseFloat($("#claimPrincipalValue").val()) || 0;

    $("#claimInterest").val("₱0.00");
    $("#claimMonths").val("Interest Waived");

    $("#claimInterestValue").val("0.00");
    $("#claimMonthsValue").val(0);
    $("#claimTotalValue").val(principal.toFixed(2));
    $("#claimTotal").val("₱" + principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
}
 else if (option === "custom") {
        $("#customInterestWrapper").show();
        $("#customInterest").val("");
    } else { // auto
        $("#customInterestWrapper").hide();
        $("#customInterest").val("");
        computeClaimInterest();
    }
});

// --- 3. Custom Interest Input ---
$("#customInterest").on("input", function() {
    const customValue = parseFloat($(this).val()) || 0;
    const principal = parseFloat($("#claimPrincipalValue").val()) || 0;

    $("#claimInterest").val("₱" + customValue.toFixed(2));
    $("#claimTotalValue").val((principal + customValue).toFixed(2));
    $("#claimTotal").val("₱" + (principal + customValue).toLocaleString(undefined, {minimumFractionDigits:2}));
});






                    // --- Set Date Claimed default to today BEFORE computing ---
                    $("#claimDate").val(today.toISOString().split('T')[0]);

                    // Initial compute when modal opens
                    computeClaimInterest();

                    // Recompute when claimDate changes
                    $("#claimDate").off("change").on("change", function () {
                        computeClaimInterest();
                    });

                    // ---------------- Populate other fields ----------------
                    fillFields({
                        "#claimPawnId": "pawn_id",
                        "#claimOwnerName": "customer_name",
                        "#claimUnitDescription": "unit_description",
                        "#claimDatePawned": "date_pawned",
                        "#claimDueDate": "current_due_date"
                    }, pawn);

                    $("#claimAmountPawned").val(principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));

                    // --- Live penalty calculation ---
                    // --- Live penalty calculation ---
                    $("#claimPenalty").off("input").on("input", function () {
                        let penalty = parseFloat($(this).val()) || 0;

                        if (penalty >= principal) {
                            Swal.fire({
                                icon: "warning",
                                title: "Invalid Penalty",
                                text: "Penalty should be less than the claim amount pawned.",
                            });
                            penalty = 0;
                            $(this).val('');
                        }

                        // Safe parse for interest
                        let interestVal = parseFloat($("#claimInterestValue").val());
                        if (isNaN(interestVal)) interestVal = 0;

                        const newTotal = principal + interestVal + penalty;

                        $("#claimTotal").val("₱" + newTotal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                        $("#claimTotalValue").val(newTotal.toFixed(2));
                    });


                    // Reset photo canvas
                    $("#claimantPhoto").val('');
                    $("#capturedCanvas")[0].getContext("2d").clearRect(0, 0, 320, 240);

                    $("#claimPawnModal").modal("show");
                })
                .fail(() => Swal.fire("Error", "Unable to fetch pawn details.", "error"));

        });


    });




    let cameraStream = document.getElementById("claimCameraStream");
    let capturedCanvas = document.getElementById("capturedCanvas");
    let capturePhotoBtn = document.getElementById("capturePhotoBtn");
    let hiddenPhotoInput = document.getElementById("claimantPhoto");

    $("#claimPawnModal").on("shown.bs.modal", function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                cameraStream.srcObject = stream;
                cameraStream.addEventListener("loadedmetadata", () => {
                    cameraStream.play();
                });
            })
            .catch((err) => {
                Swal.fire("Camera Error", "Unable to access camera: " + err, "error");
            });
    });

    capturePhotoBtn.addEventListener("click", () => {
        let context = capturedCanvas.getContext("2d");
        context.drawImage(cameraStream, 0, 0, capturedCanvas.width, capturedCanvas.height);

        let photoData = capturedCanvas.toDataURL("image/png");
        hiddenPhotoInput.value = photoData;
        Swal.fire("Success", "Photo captured!", "success");
    });

    $("#claimPawnModal").on("hidden.bs.modal", function () {
        let stream = cameraStream.srcObject;
        if (stream) {
            let tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
        }
        cameraStream.srcObject = null;
    });




    // Submit claim form
    $("#claimPawnForm").on("submit", function (e) {
        e.preventDefault();

        if (!hiddenPhotoInput.value) {
            return Swal.fire("Error", "Please capture claimant photo before submitting.", "error");
        }

        const formData = $(this).serialize();

        Swal.fire({
            title: "Confirm Claim?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Claim"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/pawn_claim_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Claimed!", response.message, "success").then(() => {
                            $("#claimPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();


                            //  Auto-print receipt after successful claim
                            if (response.pawn_id) {
                                //  Directly open the print receipt page
                                let printUrl = "../processes/receipt_print.php?pawn_id=" + response.pawn_id;
                                window.open(printUrl, "_blank"); // open in new tab for printing
                            }

                        });
                    } else {
                        Swal.fire("Error", response.message || "Unable to claim pawn.", "error");
                    }
                }, "json").fail(() => Swal.fire("Error", "Server error while processing claim.", "error"));
            }
        });
    });




    

});
