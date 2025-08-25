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
                const datePawned = new Date(pawn.date_pawned);
                const now = new Date();
                const months = Math.max(1, Math.ceil((now - datePawned) / (1000 * 60 * 60 * 24 * 30)));

                const principal = parseFloat(pawn.amount_pawned);
                const interestRate = parseFloat(data.branch_interest) || 6;
                const interest = principal * interestRate * months;

                // initial total (no penalty yet)
                let total = principal + interest;

                // Fill visible fields
                fillFields({
                    "#claimPawnId": "pawn_id",
                    "#claimOwnerName": "customer_name",
                    "#claimUnitDescription": "unit_description",
                    "#claimDatePawned": "date_pawned"
                }, pawn);

                $("#claimAmountPawned").val(principal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimMonths").val(months + " month(s)");
                $("#claimInterest").val("₱" + interest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $("#claimTotal").val("₱" + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));

                // Fill hidden fields
                $("#claimInterestRate").val(interestRate);
                $("#claimInterestValue").val(interest.toFixed(2));
                $("#claimPrincipalValue").val(principal.toFixed(2));
                $("#claimTotalValue").val(total.toFixed(2));
                $("#claimMonthsValue").val(months);

                // 🔹 Add live penalty calculation
                $("#claimPenalty").off("input").on("input", function () {
                    const penalty = parseFloat($(this).val()) || 0;
                    const newTotal = principal + interest + penalty;

                    $("#claimTotal").val("₱" + newTotal.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                    $("#claimTotalValue").val(newTotal.toFixed(2)); // hidden field for backend
                });


                // Reset photo
                $("#claimantPhoto").val('');
                $("#capturedCanvas").get(0).getContext("2d").clearRect(0, 0, 320, 240);

                // Show modal
                $("#claimPawnModal").modal("show");
            })
            .fail(() => Swal.fire("Error", "Unable to fetch pawn details.", "error"));
    });

    // Webcam Capture for Claimant Photo
    // Initialize webcam stream and capture functionality
    let cameraStream = document.getElementById("cameraStream");
    let capturedCanvas = document.getElementById("capturedCanvas");
    let capturePhotoBtn = document.getElementById("capturePhotoBtn");
    let hiddenPhotoInput = document.getElementById("claimantPhoto");

    // Start webcam when modal opens
    $("#claimPawnModal").on("shown.bs.modal", function () {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then((stream) => {
                cameraStream.srcObject = stream;
            })
            .catch((err) => {
                Swal.fire("Camera Error", "Unable to access camera: " + err, "error");
            });
    });

    // Capture photo
    capturePhotoBtn.addEventListener("click", () => {
        let context = capturedCanvas.getContext("2d");
        context.drawImage(cameraStream, 0, 0, capturedCanvas.width, capturedCanvas.height);

        // Save to hidden input as base64
        let photoData = capturedCanvas.toDataURL("image/png");
        hiddenPhotoInput.value = photoData;
        Swal.fire("Success", "Photo captured!", "success");
    });

    // Stop camera when modal closes
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

                           
                            // 🔹 Auto-print receipt after successful claim
                            if (response.pawn_id) {
                                // Fetch full claim details before printing
                                $.ajax({
                                    url: "../api/claim_view.php",
                                    type: "GET",
                                    data: { pawn_id: response.pawn_id },
                                    dataType: "json",
                                    success: function (res) {
                                        if (res.status === "success") {
                                            printClaimReceipt(res.data);
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
