$(document).ready(function () {
    let originalAmount = 0;

    // When button clicked, load data into modal
    $(document).on("click", ".addPawnAmountBtn", function () {
        let pawnId = $(this).data("id");

        $.ajax({
            url: "../api/pawn_get.php",
            type: "GET",
            data: { pawn_id: pawnId },
            dataType: "json", // tell jQuery to auto-parse JSON
            success: function (res) {
                if (res.status === "success") {
                    let data = res.pawn;

                    $("#pawnOwner").text(data.customer_name);
                    $("#pawnItem").text(data.unit_description);
                    $("#pawnCategory").text(data.category);
                    $("#pawnOriginalAmount").text(Number(data.amount_pawned).toLocaleString());
                    $("#pawnDueDate").text(data.current_due_date);

                    // keep reference for new total
                    originalAmount = parseFloat(data.amount_pawned);

                    $("#pawnAmountInput").val(100);
                    updateNewAmount();

                    $("#addPawnAmountModal").modal("show");
                } else {
                    alert(res.message);
                }
            }
        });

    });


   let stepAmount = 1000; // default step (and input) is 1000

function updateNewAmount() {
    let addAmount = parseFloat($("#pawnAmountInput").val()) || 0;
    let newAmount = originalAmount + addAmount;
    $("#pawnNewAmount").text(newAmount.toLocaleString());
}

// Increment / Decrement
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

// Quick select
$("#quickAmountSelect").on("change", function () {
    stepAmount = parseInt($(this).val()); 
    $("#pawnAmountInput").val(stepAmount); 
    updateNewAmount();
});

// Manual typing
$("#pawnAmountInput").on("input", function () {
    let val = parseInt($(this).val()) || 0;
    if (val < stepAmount) {
        $(this).val(stepAmount);
    }
    updateNewAmount();
});

// Reset button
$("#resetPawnAmount").on("click", function () {
    $("#pawnAmountInput").val(stepAmount); // back to current step (default 1000)
    updateNewAmount();
});

// Initialize default
$("#pawnAmountInput").val(stepAmount);
updateNewAmount();


    // Confirm
    $("#confirmAddPawnAmount").on("click", function () {
        let addAmount = parseFloat($("#pawnAmountInput").val());

        // Submit via AJAX
        $.ajax({
            url: "../processes/add_pawn_amount.php",
            type: "POST",
            data: {
                pawn_id: $(".addPawnAmountBtn").data("id"),
                add_amount: addAmount
            },
            success: function (response) {
                alert("Pawn amount updated!");
                $("#addPawnAmountModal").modal("hide");
                location.reload(); // refresh table
            }
        });
    });
});
