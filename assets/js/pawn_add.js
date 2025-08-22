// add pawn script
    // Initialize Select2 for customer selection
    $(document).ready(function () {

        $('#addPawnModal').on('shown.bs.modal', function () {
            $('#customer_id').select2({
                placeholder: 'Search for pawner...',
                dropdownParent: $('#addPawnModal'),
                ajax: {
                    url: 'customer_search.php',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { search: params.term || '' }; // pass search term
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
                minimumInputLength: 0, // show all customers immediately
                templateResult: formatCustomer,
                templateSelection: formatCustomerSelection,
                allowClear: true
            });

            // Format how each customer appears in the dropdown
            function formatCustomer(customer) {
                if (customer.loading) return customer.text;

                // Create a jQuery object so HTML renders correctly
                var $container = $(
                    "<div class='select2-result-customer'>" +
                    "<div class='fw-bold'>" + customer.text + "</div>" +
                    (customer.address ? "<small>Address: " + customer.address + "</small>" : "") +
                    (customer.contact_no ? " | <small>Contact: " + customer.contact_no + "</small>" : "") +

                    "</div>"
                );

                return $container;
            }

            // Format selected customer (shown in the input)
            function formatCustomerSelection(customer) {
                return customer.text || customer.id || '';
            }


        });

        // Toggle new customer fields and required attribute
        $('#addNewCustomer').change(function () {
            if ($(this).is(':checked')) {
                // Show new customer fields
                $('#newCustomerFields').show();
                $('#customer_id').prop('disabled', true);

                // Make customer_name required
                $('input[name="customer_name"]').prop('required', true);
            } else {
                // Hide new customer fields
                $('#newCustomerFields').hide();
                $('#customer_id').prop('disabled', false);

                // Make customer_name optional
                $('input[name="customer_name"]').prop('required', false);
            }
        });


        // Fill contact/address on selection
        $('#customer_id').on('select2:select', function (e) {
            let data = e.params.data;
            $('input[name="contact_no"]').val(data.contact_no || '');
            $('input[name="address"]').val(data.address || '');

            // Disable Add New Pawner when a customer is selected
            $('#addNewCustomer').prop('disabled', true).prop('checked', false);
            $('#newCustomerFields').hide();
            $('input[name="customer_name"]').prop('required', false);
        });

        // When cleared, re-enable Add New Pawner
        $('#customer_id').on('select2:clear', function () {
            $('input[name="contact_no"]').val('');
            $('input[name="address"]').val('');

            // Re-enable Add New Pawner
            $('#addNewCustomer').prop('disabled', false);
        });

        // optional
        // Prevent accessibility warning: blur any focused element inside the modal before it hides
        $('#addPawnModal').on('hide.bs.modal', function () {
            const activeEl = document.activeElement;
            if (activeEl && $(activeEl).closest('#addPawnModal').length) {
                activeEl.blur();
            }
        });



    });

    // Add Pawn form submission
    $("#addPawnForm").on("submit", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Confirm Add Pawn?",
            text: "This will save the pawned item.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Save it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "pawn_add_process.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.status === "success") {
                            Swal.fire("Success", response.message, "success");
                            $("#addPawnModal").modal("hide");
                            $("#addPawnForm")[0].reset();
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

