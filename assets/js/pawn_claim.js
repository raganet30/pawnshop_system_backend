

    // When clicking Claim button
    $(document).ready(function () {
        $('#customer_id').select2({
            placeholder: 'Search or add new customer',
            ajax: {
                url: 'customer_search.php',
                dataType: 'json',
                delay: 250,
                data: params => ({ term: params.term }),
                processResults: data => ({ results: data })
            },
            minimumInputLength: 1,
            allowClear: true,
            tags: true  // allow entering new names
        });

        // Handle selection
        $('#customer_id').on('select2:select', function (e) {
            let data = e.params.data;

            if (data.id) {
                // Existing customer: hide extra fields, auto-fill contact & address
                $('#newCustomerFields').hide();
                $('input[name="contact_no"]').val(data.contact_no || '');
                $('input[name="address"]').val(data.address || '');
            } else {
                // New customer typed: show extra fields
                $('#newCustomerFields').show();
                $('input[name="contact_no"]').val('');
                $('input[name="address"]').val('');
            }
        });
    });




    $('#addAmountPawnedVisible').on('input', function () {
        let raw = $(this).val().replace(/[^0-9.]/g, '');
        $('#addAmountPawned').val(raw);
    });



    // Submit claim form
    $("#claimPawnForm").on("submit", function (e) {
        e.preventDefault();

        // ✅ Ensure claimant photo is captured
        if (!$("#claimantPhoto").val()) {
            Swal.fire("Error", "Please capture claimant photo before submitting.", "error");
            return false;
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
                $.post("pawn_claim_process.php", formData, function (response) {
                    if (response.status === "success") {
                        Swal.fire("Claimed!", response.message, "success").then(() => {
                            $("#claimPawnModal").modal("hide");
                            $("#pawnTable").DataTable().ajax.reload();
                        });
                    } else {
                        Swal.fire("Error", response.message, "error");
                    }
                }, "json");
            }
        });
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






