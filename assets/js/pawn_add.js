// add pawn script
// Initialize Select2 for customer selection
$(document).ready(function () {

    const video = document.getElementById("cameraStream");
    const canvas = document.getElementById("pawnCapturedCanvas");
    const captureBtn = document.getElementById("pawnCapturePhotoBtn");
    const context = canvas.getContext("2d");

    // Hidden input for captured photo
    let hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "captured_photo";
    $("#addPawnForm").append(hiddenInput);

    // Initialize Select2 when modal is shown
    $('#addPawnModal').on('shown.bs.modal', function () {
        $('#customer_id').select2({
            placeholder: 'Search for pawner...',
            dropdownParent: $('#addPawnModal'),
            ajax: {
                url: '../api/customer_search.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { search: params.term || '' };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.customer_id,
                                text: item.full_name,
                                contact_no: item.contact_no,
                                address: item.address
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: formatCustomer,
            templateSelection: formatCustomerSelection,
            allowClear: true
        });

        function formatCustomer(customer) {
            if (customer.loading) return customer.text;
            return $(
                "<div class='select2-result-customer'>" +
                "<div class='fw-bold'>" + customer.text + "</div>" +
                (customer.address ? "<small>Address: " + customer.address + "</small>" : "") +
                (customer.contact_no ? " | <small>Contact: " + customer.contact_no + "</small>" : "") +
                "</div>"
            );
        }

        function formatCustomerSelection(customer) {
            return customer.text || customer.id || '';
        }

        // Start webcam stream
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function (err) {
                    console.error("Error accessing webcam: ", err);
                    Swal.fire("Error", "Unable to access camera. Please allow permissions.", "error");
                });
        }
    });

    // Stop webcam when modal closes
    $('#addPawnModal').on('hidden.bs.modal', function () {
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }
        // Reset form
        $("#pawnCapturedCanvas").get(0).getContext("2d").clearRect(0, 0, canvas.width, canvas.height);
        hiddenInput.value = "";
    });

    // Capture photo
    captureBtn.addEventListener("click", function () {
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        let dataURL = canvas.toDataURL("image/png");
        hiddenInput.value = dataURL;
    });

    // Toggle new customer fields and required attribute
    $('#addNewCustomer').change(function () {
        if ($(this).is(':checked')) {
            $('#newCustomerFields').show();
            $('#customer_id').prop('disabled', true);
            $('input[name="customer_name"]').prop('required', true);
        } else {
            $('#newCustomerFields').hide();
            $('#customer_id').prop('disabled', false);
            $('input[name="customer_name"]').prop('required', false);
        }
    });

    // Fill contact/address on selection
    $('#customer_id').on('select2:select', function (e) {
        let data = e.params.data;
        $('input[name="contact_no"]').val(data.contact_no || '');
        $('input[name="address"]').val(data.address || '');
        $('#addNewCustomer').prop('disabled', true).prop('checked', false);
        $('#newCustomerFields').hide();
        $('input[name="customer_name"]').prop('required', false);
    });

    $('#customer_id').on('select2:clear', function () {
        $('input[name="contact_no"]').val('');
        $('input[name="address"]').val('');
        $('#addNewCustomer').prop('disabled', false);
    });

    // Prevent accessibility warning
    $('#addPawnModal').on('hide.bs.modal', function () {
        const activeEl = document.activeElement;
        if (activeEl && $(activeEl).closest('#addPawnModal').length) {
            activeEl.blur();
        }
    });

    // Add Pawn form submission
    $("#addPawnForm").on("submit", function (e) {
        e.preventDefault();

        // --- Validate photo requirement ---
        let capturedPhoto = $(this).find("input[name='captured_photo']").val();
        if (!capturedPhoto) {
            Swal.fire("Photo Required", "Please capture a photo of the pawn item before saving.", "warning");
            return; // stop submission
        }

        Swal.fire({
            title: "Confirm Add Pawn?",
            text: "This will save the pawned item.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Save it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../processes/pawn_add_process.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire("Success", response.message, "success");
                            $("#addPawnModal").modal("hide");
                            $("#addPawnForm")[0].reset();
                            $("#pawnTable").DataTable().ajax.reload();

                            // clear canvas + hidden input
                            $("#pawnCapturedCanvas")[0].getContext("2d").clearRect(0, 0, 320, 240);
                            $("#addPawnForm input[name='captured_photo']").remove();

                            //  Auto-open print page
                            if (response.pawn_id) {
                                let printUrl = "../processes/pawn_item_print.php?id=" + response.pawn_id;
                                window.open(printUrl, "_blank");
                            }
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    },

                    error: function () {
                        Swal.fire("Error", "Something went wrong.", "error");
                    }
                });
            }
        });
    });



});
