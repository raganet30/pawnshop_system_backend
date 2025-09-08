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
const interestRate = parseFloat(data.branch_interest) || 0.06; // decimal
const today = new Date();

// --- Determine default interest based on tubo and partial payments ---
let totalInterest = 0;
let lastTuboEnd = null;
let lastPartialDate = null;

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
        lastTuboEnd = t.period_end; // track latest tubo period_end
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
        lastPartialDate = p.date_paid; // track latest partial payment date
    });
}
$("#partialPaymentsTable tbody").html(partialRows);

// --- Determine start date for interest calculation ---
let startDate;
if (lastTuboEnd) {
    startDate = lastTuboEnd;
} else if (lastPartialDate) {
    startDate = lastPartialDate;
} else {
    startDate = pawn.date_pawned;
}

// Convert to Date objects
const startDateObj = new Date(startDate);
const todayObj = today;

// --- Waive interest if latest tubo covers today ---
let waiveInterest = false;
if (lastTuboEnd) {
    const lastTuboEndObj = new Date(lastTuboEnd);
    if (lastTuboEndObj >= todayObj) {
        waiveInterest = true;
    }
}

// --- Calculate months covered only if not waived ---
let monthsCovered = 0;
if (!waiveInterest) {
    monthsCovered = Math.max(1, Math.ceil((todayObj - startDateObj) / (1000*60*60*24*30))); // min 1 month
    totalInterest = principal * interestRate * monthsCovered;
} else {
    totalInterest = 0;
}


 // Compute months covered (minimum 1 month)
                        let datePawned = new Date(pawn.date_pawned);
                        let claimtoday = new Date();
                        let claimdiffMonths =
                            (claimtoday.getFullYear() - datePawned.getFullYear()) * 12 +
                            (claimtoday.getMonth() - datePawned.getMonth());
                        if (claimtoday.getDate() > datePawned.getDate()) claimdiffMonths++;
                        if (claimdiffMonths < 1) claimdiffMonths = 1;

// Fill visible fields
fillFields({
    "#claimPawnId": "pawn_id",
    "#claimOwnerName": "customer_name",
    "#claimUnitDescription": "unit_description",
    "#claimDatePawned": "date_pawned"
}, pawn);

$("#claimAmountPawned").val(principal.toLocaleString(undefined, {minimumFractionDigits:2}));
$("#claimMonths").val(claimdiffMonths + " month(s)");
$("#claimInterest").val("₱" + totalInterest.toLocaleString(undefined, {minimumFractionDigits:2}));
$("#claimTotal").val("₱" + (principal + totalInterest).toLocaleString(undefined, {minimumFractionDigits:2}));

// Hidden fields for backend
$("#claimPrincipalValue").val(principal.toFixed(2));
$("#claimInterestValue").val(totalInterest.toFixed(2));
$("#claimTotalValue").val((principal + totalInterest).toFixed(2));

                // --- Set Date Claimed default to today ---
                $("#claimDate").val(today.toISOString().split('T')[0]);

                // --- Live penalty calculation ---
                $("#claimPenalty").off("input").on("input", function() {
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
                    const newTotal = principal + totalInterest + penalty;
                    $("#claimTotal").val("₱" + newTotal.toLocaleString(undefined, {minimumFractionDigits:2}));
                    $("#claimTotalValue").val(newTotal.toFixed(2));
                });

                // Reset photo canvas
                $("#claimantPhoto").val('');
                $("#capturedCanvas")[0].getContext("2d").clearRect(0,0,320,240);

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
