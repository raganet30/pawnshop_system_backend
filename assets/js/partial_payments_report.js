$(document).ready(function() {
    let partialTable = $('#partialPaymentsTable').DataTable({
        ajax: {
            url: '../api/partial_payments_list.php',
            type: 'POST',
            data: function(d) {
                d.branch_id = $('#partial_branchFilter').val() || '';
                d.from_date = $('#partial_fromDate').val();
                d.to_date = $('#partial_toDate').val();
            }
        },
        columns: [
            { data: 'serial' },
            { 
                data: 'date_paid',
                render: function(data) {
                    if (!data) return '';
                    let date = new Date(data);
                    return date.toISOString().split('T')[0];
                }
            },
            { data: 'customer' },
            { data: 'item' },
            { data: 'amount_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
            // { data: 'interest_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
            // { data: 'principal_paid', render: d => '₱' + parseFloat(d).toFixed(2) },
            { data: 'remaining_balance', render: d => '₱' + parseFloat(d).toFixed(2) },
            { 
                data: 'status',
                render: function(d) {
                    if(d === 'active') return '<span class="badge bg-success">Active</span>';
                    if(d === 'settled') return '<span class="badge bg-info">Settled</span>';
                    return '<span class="badge bg-dark">Unknown</span>';
                }
            }
        ],
        order: [[1,'desc']],
        footerCallback: function(row, data, start, end, display) {
            let api = this.api();
            let intVal = i => typeof i === 'string' ? i.replace(/[₱,]/g,'')*1 : i*1;

            let totalPayment = api.column(4).data().reduce((a,b)=>intVal(a)+intVal(b),0);
            let totalInterest = api.column(5).data().reduce((a,b)=>intVal(a)+intVal(b),0);
            let totalPrincipal = api.column(6).data().reduce((a,b)=>intVal(a)+intVal(b),0);

            $(api.column(4).footer()).html('₱'+totalPayment.toLocaleString(undefined,{minimumFractionDigits:2}));
            // $(api.column(5).footer()).html('₱'+totalInterest.toLocaleString(undefined,{minimumFractionDigits:2}));
            // $(api.column(6).footer()).html('₱'+totalPrincipal.toLocaleString(undefined,{minimumFractionDigits:2}));
        }
    });

    $('#partial_filterBtn').on('click', ()=> partialTable.ajax.reload());
    $('#partial_resetBtn').on('click', ()=>{
        $('#partial_branchFilter').val('');
        $('#partial_fromDate').val('');
        $('#partial_toDate').val('');
        partialTable.ajax.reload();
    });
});
