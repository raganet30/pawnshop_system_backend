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

                        let lastTuboEnd = null;
                        let lastPartialDate = null;

                        // --- Get last tubo payment period_end ---
                        if (Array.isArray(data.tubo_history) && data.tubo_history.length > 0) {
                            let lastTuboIndex = data.tubo_history.length - 1;
                            lastTuboEnd = parseYMD(data.tubo_history[lastTuboIndex].period_end);
                        }

                        // --- Get last partial payment date ---
                        if (Array.isArray(data.partial_history) && data.partial_history.length > 0) {
                            let lastIndex = data.partial_history.length - 1;
                            lastPartialDate = parseYMD(data.partial_history[lastIndex].date_paid);
                        }

                        let claimDateStr = $("#claimDate").val();
                        let todayLocal = claimDateStr ? parseYMD(claimDateStr) : new Date();

                        // --- Compute start date ---
                        let startDate = new Date(pawn.date_pawned);
                        if (lastTuboEnd && lastTuboEnd > startDate) startDate = lastTuboEnd;
                        if (lastPartialDate && lastPartialDate > startDate) {
                            startDate = new Date(lastPartialDate);
                            startDate.setMonth(startDate.getMonth() + 1); // skip the already-covered month
                        }

                        // --- Check for waived interest ---
                        let waiveInterest = false;
                        if (lastTuboEnd && lastTuboEnd >= todayLocal) {
                            waiveInterest = true;
                        }
                        if (!waiveInterest && lastPartialDate) {
                            const daysSince = Math.floor((todayLocal - lastPartialDate) / (1000 * 60 * 60 * 24));
                            if (daysSince < 31) waiveInterest = true;
                        }

                        // --- Calculate months & interest ---
                        let claimdiffMonths = 0;
                        let totalInterest = 0;
                        if (!waiveInterest) {
                            claimdiffMonths = (todayLocal.getFullYear() - startDate.getFullYear()) * 12 +
                                (todayLocal.getMonth() - startDate.getMonth());
                            if (todayLocal.getDate() > startDate.getDate()) claimdiffMonths++;
                            if (claimdiffMonths < 1) claimdiffMonths = 1;
                            totalInterest = principal * interestRate * claimdiffMonths;
                        }

                        // --- Update UI ---
                        if (waiveInterest) {
                            $("#claimMonths").val("Interest waived");
                        } else {
                            $("#claimMonths").val(claimdiffMonths + " month(s)");
                        }
                        $("#claimInterest").val("₱" + totalInterest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                        $("#claimTotal").val("₱" + (principal + totalInterest).toLocaleString(undefined, { minimumFractionDigits: 2 }));

                        // Hidden fields
                        $("#claimPrincipalValue").val(principal.toFixed(2));
                        $("#claimInterestValue").val(totalInterest.toFixed(2));
                        $("#claimTotalValue").val((principal + totalInterest).toFixed(2));
                        $("#claimMonthsValue").val(claimdiffMonths);
                        $("#claimPenalty").val('0.00'); // reset penalty value
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
            confirmButtonText: "Yes, Claim it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../processes/pawn_claim_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Claimed!", response.message, "success").then(() => {
                            $("#claimPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();


                            //  Auto-print receipt after successful claim
                            if (response.pawn_id) {
                                // Fetch full claim details before printing
                                $.ajax({
                                    url: "../api/claim_view.php",
                                    type: "GET",
                                    data: { pawn_id: response.pawn_id },
                                    dataType: "json",
                                    success: function (res) {
                                        if (res.status === "success") {
                                            // printClaimReceipt(res.data);

                                            // Open print preview in new tab

                                        } else {
                                            console.error("Failed to fetch claim details:", res.message);
                                        }
                                    },
                                    error: function () {
                                        console.error("Error fetching claim details for printing.");
                                    }
                                });
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
