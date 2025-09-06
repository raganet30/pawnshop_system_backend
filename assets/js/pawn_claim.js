$(function () {

    // Utility: Fill input fields
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
                const tuboHistory = data.tubo_payments || [];
                const partialHistory = data.partial_payments || [];

                const datePawned = new Date(pawn.date_pawned);
                const now = new Date();

                // Normalize both to midnight to avoid hour differences
                datePawned.setHours(0, 0, 0, 0);
                now.setHours(0, 0, 0, 0);

                // Calculate days difference
                const daysDiff = Math.floor((now - datePawned) / (1000 * 60 * 60 * 24));

                // Convert to months (min. 1 month, assume 31-day cycle)
                const months = Math.max(1, Math.ceil(daysDiff / 31));


                const principal = parseFloat(pawn.amount_pawned);
                const interestRate = parseFloat(data.branch_interest) || 0.06;

                // --- Check if prepaid ---
                let prepaid = false;

                // 1. Check tubo payments
                if (tuboHistory.length > 0) {
                    const lastTubo = tuboHistory[0];
                    if (lastTubo.period_end && new Date(lastTubo.period_end) >= now) {
                        prepaid = true;
                    }
                }

                // 2. Check partial payments (any within the current cover period)
                if (!prepaid && partialHistory.length > 0) {
                    const currentPeriodStart = new Date(datePawned);
                    currentPeriodStart.setMonth(currentPeriodStart.getMonth() + (months - 1));
                    currentPeriodStart.setHours(0, 0, 0, 0);

                    const currentPeriodEnd = new Date(datePawned);
                    currentPeriodEnd.setMonth(currentPeriodEnd.getMonth() + months);
                    currentPeriodEnd.setHours(23, 59, 59, 999);

                    prepaid = partialHistory.some(pp => {
                        // Fix parsing
                        const ppDate = new Date(pp.created_at.replace(' ', 'T'));
                        return ppDate >= currentPeriodStart && ppDate <= currentPeriodEnd;
                    });
                }

                // --- Calculate interest ---
                let interest = principal * interestRate * months;
                if (prepaid) {
                    interest = 0; // force zero
                }

                let total = principal + interest;

                // --- Fill visible fields ---
                fillFields({
                    "#claimPawnId": "pawn_id",
                    "#claimOwnerName": "customer_name",
                    "#claimUnitDescription": "unit_description",
                    "#claimDatePawned": "date_pawned"
                }, pawn);

                $("#claimAmountPawned").val(principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimMonths").val(months + " month(s)");

                // Always show "₱0.00" if prepaid
                $("#claimInterest").val("₱" + (interest === 0 ? "0.00" : interest.toLocaleString(undefined, { minimumFractionDigits: 2 })));

                $("#claimTotal").val("₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));


                // --- Populate tubo table ---
                let tuboTbody = $("#tuboPaymentsTable tbody");
                tuboTbody.empty();
                if (tuboHistory.length > 0) {
                    tuboHistory.forEach((row, i) => {
                        tuboTbody.append(`
                            <tr>
                                <td>${i + 1}</td>
                                <td>${row.date_paid}</td>
                                <td>${row.covered_period}</td>
                                <td>₱${parseFloat(row.interest_amount).toFixed(2)}</td>
                               
                            </tr>
                        `);
                    });
                } else {
                    tuboTbody.append(`<tr><td colspan="5" class="text-center text-muted">No tubo payments</td></tr>`);
                }

                // --- Populate partial payments table ---
                let partialTbody = $("#partialPaymentsTable tbody");
                partialTbody.empty();
                if (partialHistory.length > 0) {
                    partialHistory.forEach((row, i) => {
                        partialTbody.append(`
                            <tr>
                                <td>${i + 1}</td>
                                <td>${row.created_at}</td>
                                <td>₱${parseFloat(row.amount_paid).toFixed(2)}</td>
                                <td>₱${parseFloat(row.interest_paid).toFixed(2)}</td>
                                <td>₱${parseFloat(row.principal_paid).toFixed(2)}</td>
                                <td>₱${parseFloat(row.remaining_principal).toFixed(2)}</td>
                                <td>${row.status}</td>
                             
                            </tr>
                        `);
                    });
                } else {
                    partialTbody.append(`<tr><td colspan="9" class="text-center text-muted">No partial payments</td></tr>`);
                }

                // Reset photo
                $("#claimantPhoto").val('');
                $("#capturedCanvas").get(0).getContext("2d").clearRect(0, 0, 320, 240);

                // Show modal
                $("#claimPawnModal").modal("show");
            })
            .fail(() => Swal.fire("Error", "Unable to fetch pawn details.", "error"));
    });

    // Webcam Capture for Claimant Photo
    let cameraStream = document.getElementById("cameraStream");
    let capturedCanvas = document.getElementById("capturedCanvas");
    let capturePhotoBtn = document.getElementById("capturePhotoBtn");
    let hiddenPhotoInput = document.getElementById("claimantPhoto");

    $("#claimPawnModal").on("shown.bs.modal", function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                cameraStream.srcObject = stream;
            })
            .catch((err) => {
                Swal.fire("Camera Error", "Unable to access camera: " + err, "error");
            });
    });

    capturePhotoBtn.addEventListener("click", () => {
        let context = capturedCanvas.getContext("2d");
        context.drawImage(cameraStream, 0, 0, capturedCanvas.width, capturedCanvas.height);
        hiddenPhotoInput.value = capturedCanvas.toDataURL("image/png");
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

                            // Auto-print receipt
                            if (response.pawn_id) {
                                $.ajax({
                                    url: "../api/claim_view.php",
                                    type: "GET",
                                    data: { pawn_id: response.pawn_id },
                                    dataType: "json",
                                    success: function (res) {
                                        if (res.status === "success") {
                                            printClaimReceipt(res.data);
                                        }
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
