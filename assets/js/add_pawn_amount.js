$(document).ready(function () {
    let originalAmount = 0;
    let stepAmount = 1000;        // default step
    let currentPawnId = null;     // store currently selected pawn ID

    // --- Function to update the new amount display ---
    function updateNewAmount() {
        let addAmount = parseFloat($("#pawnAmountInput").val()) || 0;
        let newAmount = originalAmount + addAmount;
        $("#pawnNewAmount").text(newAmount.toLocaleString());
    }

    // --- Open modal and load pawn data ---
    $(document).on("click", ".addPawnAmountBtn", function () {
        currentPawnId = $(this).data("id");

        $.ajax({
            url: "../api/pawn_get.php",
            type: "GET",
            data: { pawn_id: currentPawnId },
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    let data = res.pawn;

                    $("#pawnOwner").text(data.customer_name);
                    $("#pawnItem").text(data.unit_description);
                    $("#pawnCategory").text(data.category);
                    $("#pawnOriginalAmount").text(Number(data.amount_pawned).toLocaleString());
                    $("#pawnDueDate").text(data.current_due_date);

                    originalAmount = parseFloat(data.amount_pawned);

                    // Reset input and step
                    stepAmount = 1000;
                    $("#pawnAmountInput").val(stepAmount);
                    $("#quickAmountSelect").val(stepAmount);
                    updateNewAmount();

                    $("#addPawnAmountModal").modal("show");
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to fetch pawn data.', 'error');
            }
        });
    });

    // --- Increment / Decrement ---
    $("#increaseAmount").on("click", function () {
        let val = parseInt($("#pawnAmountInput").val()) || 0;
        $("#pawnAmountInput").val(val + stepAmount);
        updateNewAmount();
    });

    $("#decreaseAmount").on("click", function () {
        let val = parseInt($("#pawnAmountInput").val()) || 0;
        if (val > stepAmount) {
            $("#pawnAmountInput").val(val - stepAmount);
        } else {
            $("#pawnAmountInput").val(stepAmount);
        }
        updateNewAmount();
    });

    // --- Quick select ---
    $("#quickAmountSelect").on("change", function () {
        stepAmount = parseInt($(this).val());
        $("#pawnAmountInput").val(stepAmount);
        updateNewAmount();
    });

    // --- Manual typing (optional, remove readonly on input if you want) ---
    $("#pawnAmountInput").on("input", function () {
        let val = parseInt($(this).val()) || 0;
        if (val < stepAmount) {
            $(this).val(stepAmount);
        }
        updateNewAmount();
    });

    // --- Reset button ---
    $("#resetPawnAmount").on("click", function () {
        $("#pawnAmountInput").val(stepAmount);
        updateNewAmount();
    });

    // --- Confirm Add Amount with SweetAlert2 ---
    $("#confirmAddPawnAmount").on("click", function () {
        if (!currentPawnId) return;

        let addAmount = parseFloat($("#pawnAmountInput").val());

        Swal.fire({
            title: 'Confirm Add Amount',
            text: `Are you sure you want to add ₱${addAmount.toLocaleString()} to this pawn?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "../processes/add_pawn_amount.php",
                    type: "POST",
                    data: {
                        pawn_id: currentPawnId,
                        add_amount: addAmount
                    },
                    success: function (response) {
                        let res;
                        try {
                            res = JSON.parse(response);
                        } catch {
                            Swal.fire('Error', 'Invalid response from server.', 'error');
                            return;
                        }

                        if (res.status === "success") {
                            Swal.fire('Updated!', res.message, 'success').then(() => {
                                $("#addPawnAmountModal").modal("hide");
                                $("#pawnTable").DataTable().ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    // --- Initialize default on page load ---
    $("#pawnAmountInput").val(stepAmount);
    $("#quickAmountSelect").val(stepAmount);
    updateNewAmount();
});
