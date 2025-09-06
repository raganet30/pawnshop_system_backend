$(function () {

    // Utility: Fill input fields
    function fillFields(mapping, data) {
        for (const selector in mapping) {
            $(selector).val(data[mapping[selector]] ?? '');
        }
    }

    // --- Recalculate total with optional penalty ---
    function recalcClaimTotal(principal, interest) {
        let penalty = parseFloat($("#claimPenalty").val()) || 0;

        // Validation: Penalty must not exceed principal
        if (penalty > principal) {
            Swal.fire("Invalid Penalty", "Penalty cannot exceed the pawned amount.", "warning");
            $("#claimPenalty").val(0); // reset to 0
            penalty = 0;
        }

        const total = principal + interest + penalty;

        $("#claimTotal").val("₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        $("#claimPenaltyHidden").val(penalty); // sync hidden field for submission
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
                datePawned.setHours(0, 0, 0, 0);
                now.setHours(0, 0, 0, 0);

                const daysDiff = Math.floor((now - datePawned) / (1000 * 60 * 60 * 24));
                const months = Math.max(1, Math.ceil(daysDiff / 31));

                const principal = parseFloat(pawn.amount_pawned);
                const interestRate = parseFloat(data.branch_interest) || 0.06;

                // --- Check if prepaid ---
                let prepaid = false;

                if (tuboHistory.length > 0) {
                    const lastTubo = tuboHistory[0];
                    if (lastTubo.period_end && new Date(lastTubo.period_end) >= now) {
                        prepaid = true;
                    }
                }

                if (!prepaid && partialHistory.length > 0) {
                    const currentPeriodStart = new Date(datePawned);
                    currentPeriodStart.setMonth(currentPeriodStart.getMonth() + (months - 1));
                    currentPeriodStart.setHours(0, 0, 0, 0);

                    const currentPeriodEnd = new Date(datePawned);
                    currentPeriodEnd.setMonth(currentPeriodEnd.getMonth() + months);
                    currentPeriodEnd.setHours(23, 59, 59, 999);

                    prepaid = partialHistory.some(pp => {
                        const ppDate = new Date(pp.created_at.replace(' ', 'T'));
                        return ppDate >= currentPeriodStart && ppDate <= currentPeriodEnd;
                    });
                }

                // --- Calculate interest ---
                let interest = principal * interestRate * months;
                if (prepaid) interest = 0;

                // --- Fill visible fields ---
                fillFields({
                    "#claimPawnId": "pawn_id",
                    "#claimOwnerName": "customer_name",
                    "#claimUnitDescription": "unit_description",
                    "#claimDatePawned": "date_pawned"
                }, pawn);

                $("#claimAmountPawned").val(principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimMonths").val(months + " month(s)");
                $("#claimInterest").val("₱" + (interest === 0 ? "0.00" : interest.toLocaleString(undefined, { minimumFractionDigits: 2 })));

                // Initial total calculation
                recalcClaimTotal(principal, interest);

                // Recalculate total whenever penalty changes
                $("#claimPenalty").off("input").on("input", function () {
                    recalcClaimTotal(principal, interest);
                });

                // --- Populate tables & modal setup (tubo, partial, photo) ---
                // ... your existing code for tables & webcam remains unchanged ...

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
