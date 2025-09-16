$(document).ready(function () {

    let tuboTable = $("#tuboTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "../api/tubo_payments_list.php",
            type: "POST",
            data: function(d) {
                d.branch_id = $("#tubo_branchFilter").val() || '';
                d.from_date = $("#tubo_fromDate").val() || '';
                d.to_date = $("#tubo_toDate").val() || '';
            }
        },
        columns: [
            { data: "tubo_id" },
            { data: "item" },
            { data: "owner" },
            { data: "date_paid" },
            { 
                data: null,
                render: function (data, type, row) {
                    return `${row.period_start} to ${row.period_end}`;
                }
            },
            { data: "months_covered" },
            { 
                data: "interest_amount",
                render: $.fn.dataTable.render.number(',', '.', 2, '₱')
            }
        ],
        order: [[3, 'desc']],
        footerCallback: function ( row, data, start, end, display ) {
            let api = this.api();
            let total = api.column(6, { page:'current' }).data()
                .reduce((a,b) => parseFloat(a)+parseFloat(b),0);
            $(api.column(6).footer()).html('₱' + total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}));
        }
    });

    $("#tubo_filterBtn").on("click", function() { tuboTable.ajax.reload(); });
    $("#tubo_resetBtn").on("click", function() {
        $("#tubo_branchFilter").val('');
        $("#tubo_fromDate").val('');
        $("#tubo_toDate").val('');
        tuboTable.ajax.reload();
    });
    $("#tubo_branchFilter, #tubo_fromDate, #tubo_toDate").on("change", function(){ tuboTable.ajax.reload(); });

});
