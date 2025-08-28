<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pawnshop Reports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .tab-content { margin-top: 20px; }
    .filter-bar { margin-bottom: 15px; }
    .table-wrapper { max-height: 400px; overflow-y: auto; }
</style>
</head>
<body>
<div class="container-fluid mt-4">
    <h3>Pawnshop Reports</h3>

    <!-- Filter Bar -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <input type="date" class="form-control" id="startDate" placeholder="Start Date">
        </div>
        <div class="col-md-3">
            <input type="date" class="form-control" id="endDate" placeholder="End Date">
        </div>
        <div class="col-md-3" id="branchFilterWrapper">
            <select class="form-select" id="branchFilter">
                <option value="">All Branches</option>
                <option value="1">Branch A</option>
                <option value="2">Branch B</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" id="filterReport">Filter</button>
        </div>
    </div>

    <!-- Report Tabs -->
    <ul class="nav nav-tabs" id="reportTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pawned-tab" data-bs-toggle="tab" href="#pawned" role="tab">Pawned Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="claimed-tab" data-bs-toggle="tab" href="#claimed" role="tab">Claimed Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="forfeited-tab" data-bs-toggle="tab" href="#forfeited" role="tab">Forfeited Items</a>
        </li>
    </ul>

    <div class="tab-content mt-3">
        <!-- Pawned Items Tab -->
        <div class="tab-pane fade show active" id="pawned" role="tabpanel">
            <table id="pawnedTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Pawn ID</th>
                        <th>Customer</th>
                        <th>Unit</th>
                        <th>Category</th>
                        <th>Amount (₱)</th>
                        <th>Date Pawned</th>
                        <th>Status</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Amount:</th>
                        <th></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Claimed Items Tab -->
        <div class="tab-pane fade" id="claimed" role="tabpanel">
            <table id="claimedTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Pawn ID</th>
                        <th>Customer</th>
                        <th>Unit</th>
                        <th>Category</th>
                        <th>Amount Paid (₱)</th>
                        <th>Date Claimed</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Amount:</th>
                        <th></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Forfeited Items Tab -->
        <div class="tab-pane fade" id="forfeited" role="tabpanel">
            <table id="forfeitedTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Pawn ID</th>
                        <th>Customer</th>
                        <th>Unit</th>
                        <th>Category</th>
                        <th>Amount Forfeited (₱)</th>
                        <th>Date Forfeited</th>
                        <th>Branch</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total Amount:</th>
                        <th></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Include DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    function initDataTable(tableId, ajaxUrl) {
        return $('#' + tableId).DataTable({
            ajax: {
                url: ajaxUrl,
                type: 'POST',
                data: function(d) {
                    d.startDate = $('#startDate').val();
                    d.endDate = $('#endDate').val();
                    d.branch_id = $('#branchFilter').val();
                }
            },
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5', 'print'
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var total = api
                    .column(4, { page: 'current' })
                    .data()
                    .reduce(function(a, b) {
                        return a + parseFloat(b.toString().replace(/,/g, '')) || 0;
                    }, 0);
                $(api.column(4).footer()).html('₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2 }));
            },
            columnDefs: [
                { targets: [4], className: 'text-end' } // Amount column right-align
            ]
        });
    }

    var pawnedTable = initDataTable('pawnedTable', '../api/pawn_list.php');
    var claimedTable = initDataTable('claimedTable', '../api/claim_list.php');
    var forfeitedTable = initDataTable('forfeitedTable', '../api/forfeit_list.php');

    $('#filterReport').on('click', function() {
        pawnedTable.ajax.reload();
        claimedTable.ajax.reload();
        forfeitedTable.ajax.reload();
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
