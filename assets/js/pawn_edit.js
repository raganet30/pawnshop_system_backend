$(document).on("click", ".editPawnBtn", function (e) {
    e.preventDefault();
    let pawnId = $(this).data("id");

    $.ajax({
        url: "../api/pawn_get.php",
        type: "GET",
        data: { pawn_id: pawnId },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let pawn = response.pawn;

                // Fill modal fields
                $("#editPawnId").val(pawn.pawn_id);
                $("#editCustomerName").val(pawn.customer_name);
                $("#editContactNo").val(pawn.contact_no);
                $("#editAddress").val(pawn.address);
                $("#editUnitDescription").val(pawn.unit_description);
                $("#editCategory").val(pawn.category);
                $("#editNotes").val(pawn.notes);
                $("#editDatePawned").val(pawn.date_pawned);

                // Amount fields: hidden raw value + formatted visible value
                $("#editAmountPawned").val(pawn.amount_pawned);
                $("#editAmountPawnedVisible").val(
                    Number(pawn.amount_pawned).toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                );

                // Attach currency formatter
                attachCurrencyFormatter(
                    document.getElementById('editAmountPawnedVisible'),
                    document.getElementById('editAmountPawned')
                );

                // 🖼️ Load pawn item photo
                if (pawn.photo_path && pawn.photo_path !== "") {
                    $("#edit_pawn_preview").attr("src", "../" + pawn.photo_path);
                } else {
                    $("#edit_pawn_preview").attr("src", "assets/img/no-image.png");
                }

                // Show modal
                $("#editPawnModal").modal("show");

                
                // --- Photo preview if file selected ---
                $("#editPawnPhoto").on("change", function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function (event) {
                            $("#edit_pawn_preview").attr("src", event.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                });

                // --- Handle capture new photo ---
                $("#editPawnCaptureBtn").on("click", function () {
                    navigator.mediaDevices.getUserMedia({ video: true }).then(stream => {
                        let video = document.createElement("video");
                        video.srcObject = stream;
                        video.play();

                        Swal.fire({
                            title: 'Capture Photo',
                            html: '<video id="captureVideo" width="100%" height="240" autoplay playsinline></video>',
                            showCancelButton: true,
                            confirmButtonText: 'Capture',
                            didOpen: () => {
                                document.getElementById("captureVideo").srcObject = stream;
                            },
                            willClose: () => {
                                stream.getTracks().forEach(track => track.stop());
                            }
                        }).then(result => {
                            if (result.isConfirmed) {
                                let canvas = document.getElementById("editPawnCanvas");
                                let ctx = canvas.getContext("2d");
                                ctx.drawImage(document.getElementById("captureVideo"), 0, 0, canvas.width, canvas.height);
                                let dataUrl = canvas.toDataURL("image/png");

                                // Update preview
                                $("#edit_pawn_preview").attr("src", dataUrl);

                                // Remove any existing hidden input first
                                $("#editPawnForm input[name='captured_photo']").remove();

                                // Store captured photo for backend
                                $("#editPawnForm").append('<input type="hidden" name="captured_photo" value="' + dataUrl + '">');
                            }
                        });
                    });
                });




            } else {
                Swal.fire("Error", response.error || "Could not fetch pawn data.", "error");
            }
        },
        error: function () {
            Swal.fire("Error", "Failed to fetch pawn data.", "error");
        }
    });
});




// Handle Edit Form submit with confirmation
$("#editPawnForm").on("submit", function (e) {
    e.preventDefault();

    Swal.fire({
        title: 'Save changes?',
        text: "Do you want to update this pawn's information?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../processes/pawn_edit_process.php",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $("#editPawnModal").modal("hide");
                        $("#pawnTable").DataTable().ajax.reload();
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
