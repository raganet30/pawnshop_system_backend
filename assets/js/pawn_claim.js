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
                <td>${parseFloat(p.interest_paid).toFixed(2)}</td>
                <td>${parseFloat(p.principal_paid).toFixed(2)}</td>
                <td>${parseFloat(p.remaining_principal).toFixed(2)}</td>
                <td>${p.status}</td>
            </tr>`;
                        });
                    }
                    $("#partialPaymentsTable tbody").html(partialRows);

                    // ---------------- Interest Computation ----------------
                    function computeClaimInterest() {
                        function parseYMD(ymd) {
                            if (!ymd) return null;
                            const parts = String(ymd).split("-").map(Number);
                            return new Date(parts[0], parts[1] - 1, parts[2]); // local midnight
                        }
                        function formatDateLocal(date) {
                            return date ? date.toLocaleDateString("en-CA") : null;
                        }

                        // detect presence of partial/tubo either by flags on pawn or by history arrays
                        const hasPartial = (pawn.has_partial_payments == 1) ||
                            (Array.isArray(data.partial_history) && data.partial_history.length > 0);
                        const hasTubo = (pawn.has_tubo_payments == 1) ||
                            (Array.isArray(data.tubo_history) && data.tubo_history.length > 0);

                        // claim date (from input) or today
                        let claimDateStr = $("#claimDate").val();
                        let todayLocal = claimDateStr ? parseYMD(claimDateStr) : new Date();
                        if (!todayLocal || isNaN(todayLocal.getTime())) todayLocal = new Date();

                        // parse pawned date (start point)
                        let startDate = parseYMD(pawn.date_pawned) || new Date(pawn.date_pawned);
                        if (!startDate || isNaN(startDate.getTime())) startDate = new Date();

                        // default values
                        let claimdiffMonths = 0;
                        let totalInterest = 0;
                        let waiveInterest = false;

                        // -------- Step 1: if NO partials, NO tubo, and not yet claimed => compute normal interest --------
                        if (!hasPartial && !hasTubo && pawn.status == 'pawned') {
                            claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                                (todayLocal.getMonth() - startDate.getMonth());

                            if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                            if (claimdiffMonths < 1) claimdiffMonths = 1;

                            totalInterest = principal * interestRate * claimdiffMonths;

                            console.debug("STEP 1 (no partial/no tubo):", {
                                startDate: formatDateLocal(startDate),
                                claimDate: formatDateLocal(todayLocal),
                                claimdiffMonths,
                                totalInterest
                            });
                        }

                        // -------- Step 2: if HAS tubo payments --------
                        else if (hasTubo && pawn.status == 'pawned') {
                            let lastTuboEnd = null;
                            if (Array.isArray(data.tubo_history) && data.tubo_history.length > 0) {
                                let lastTuboIndex = data.tubo_history.length - 1;
                                lastTuboEnd = parseYMD(data.tubo_history[lastTuboIndex].period_end);
                            }

                            if (lastTuboEnd) {
                                if (todayLocal <= lastTuboEnd) {
                                    //  within tubo coverage → waive interest
                                    waiveInterest = true;
                                    claimdiffMonths = 0;
                                    totalInterest = 0;

                                    console.debug("STEP 2 (tubo, waived):", {
                                        claimDate: formatDateLocal(todayLocal),
                                        lastTuboEnd: formatDateLocal(lastTuboEnd),
                                        totalInterest
                                    });
                                } else {
                                    //  tubo coverage expired → compute from tubo due date forward
                                    startDate = new Date(lastTuboEnd);
                                    startDate.setMonth(startDate.getMonth());

                                    claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                                        (todayLocal.getMonth() - startDate.getMonth());

                                    if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                                    if (claimdiffMonths < 1) claimdiffMonths = 1;

                                    totalInterest = principal * interestRate * claimdiffMonths;

                                    console.debug("STEP 2 (tubo, compute):", {
                                        startDate: formatDateLocal(startDate),
                                        claimDate: formatDateLocal(todayLocal),
                                        claimdiffMonths,
                                        totalInterest
                                    });
                                }
                            }
                        }


                        // -------- STEP 3: Partial payments --------
                        else if (hasPartial) {
                            let currentDueDate = parseYMD(pawn.current_due_date);

                            if (currentDueDate && todayLocal <= currentDueDate) {
                                // still within coverage → waive
                                waiveInterest = true;
                                console.debug("STEP 3 (partial, waived):", {
                                    currentDueDate: formatDateLocal(currentDueDate),
                                    claimDate: formatDateLocal(todayLocal)
                                });
                            } else if (currentDueDate) {
                                // start counting from current due date
                                startDate = new Date(currentDueDate);

                                claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                                    (todayLocal.getMonth() - startDate.getMonth());

                                if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                                if (claimdiffMonths < 1) claimdiffMonths = 1;

                                totalInterest = principal * interestRate * claimdiffMonths;

                                console.debug("STEP 3 (partial, compute):", {
                                    startDate: formatDateLocal(startDate),
                                    claimDate: formatDateLocal(todayLocal),
                                    claimdiffMonths,
                                    totalInterest
                                });
                            }
                        }


                        // -------- STEP 4: Both tubo + partial payments --------
                        else if (hasPartial && hasTubo) {
                            let currentDueDate = parseYMD(pawn.current_due_date);
                            let tuboDueDate = parseYMD(data.tubo_history[data.tubo_history.length - 1].new_due_date);

                            // pick whichever is later
                            let latestDueDate = null;
                            if (currentDueDate && tuboDueDate) {
                                latestDueDate = (tuboDueDate > currentDueDate) ? tuboDueDate : currentDueDate;
                            } else {
                                latestDueDate = currentDueDate || tuboDueDate; // whichever exists
                            }

                            if (latestDueDate && todayLocal <= latestDueDate) {
                                // still covered → waive
                                waiveInterest = true;
                                console.debug("STEP 4 (partial+tubo, waived):", {
                                    latestDueDate: formatDateLocal(latestDueDate),
                                    claimDate: formatDateLocal(todayLocal)
                                });
                            } else if (latestDueDate) {
                                // compute from latest due date forward
                                startDate = new Date(latestDueDate);

                                claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                                    (todayLocal.getMonth() - startDate.getMonth());

                                if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                                if (claimdiffMonths < 1) claimdiffMonths = 1;

                                totalInterest = principal * interestRate * claimdiffMonths;

                                console.debug("STEP 4 (partial+tubo, compute):", {
                                    startDate: formatDateLocal(startDate),
                                    claimDate: formatDateLocal(todayLocal),
                                    claimdiffMonths,
                                    totalInterest
                                });
                            }
                        }






                        // --- Update UI (shared) ---
                        if (waiveInterest) {
                            $("#claimMonths").val("Interest waived");
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
                        $("#claimPenalty").val('0.00'); // reset penalty
                    }


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
