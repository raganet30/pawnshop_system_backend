<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
include '../views/header.php';
// session checker
require_once "../processes/session_check.php";
checkSessionTimeout($pdo);

?>


<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include '../views/sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <!-- Top Navigation -->
        <?php include '../views/topbar.php'; ?>

        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Reports</h2>

            </div>

            <div class="container-fluid py-4">
                <!-- <h3 class="mb-4">Reports</h3> -->

                <!-- Tabs for Reports -->
                <ul class="nav nav-tabs mb-3" id="reportsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pawned-tab" data-bs-toggle="tab" data-bs-target="#pawned"
                            type="button" role="tab" aria-controls="pawned" aria-selected="true">Pawned Items</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="claimed-tab" data-bs-toggle="tab" data-bs-target="#claimed"
                            type="button" role="tab" aria-controls="claimed" aria-selected="false">Claims</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="forfeited-tab" data-bs-toggle="tab" data-bs-target="#forfeited"
                            type="button" role="tab" aria-controls="forfeited" aria-selected="false">Forfeited</button>
                    </li>
                </ul>

                <div class="tab-content" id="reportsTabContent">
                    <!-- Pawned Items Tab -->
                    <div class="tab-pane fade show active" id="pawned" role="tabpanel" aria-labelledby="pawned-tab">
                        <div class="row mb-3">
                            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                                <div class="col-md-3">
                                    <label for="branchFilter" class="form-label">Branch:</label>
                                    <select id="branchFilter" class="form-select">
                                        <option value="">All Branches</option>
                                        <?php
                                        $stmt = $pdo->query("SELECT branch_id, branch_name FROM branches ORDER BY branch_name");
                                        while ($branch = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<option value="' . $branch['branch_id'] . '">' . htmlspecialchars($branch['branch_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-2">
                                <label for="fromDate" class="form-label">From:</label>
                                <input type="date" id="fromDate" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="toDate" class="form-label">To:</label>
                                <input type="date" id="toDate" class="form-control">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button id="filterBtn" class="btn btn-primary me-2">Filter</button>
                                <button id="resetBtn" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="pawned_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="pawned_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="pawned_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="pawnedTable"
                                        style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Owner</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Contact No.</th>
                                                <th>Address</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Total Pawned Amount</th>
                                                <th id="pawned_total_amount">0.00</th>
                                                <th colspan="3"></th>

                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <!-- Populate via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Claims Tab -->
                    <div class="tab-pane fade" id="claimed" role="tabpanel" aria-labelledby="claimed-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="claimedFilters" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Branch</label>
                                        <select class="form-select" name="branch_id" id="claimed_branch">
                                            <option value="">All Branches</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" class="form-control" name="date_from" id="claimed_date_from">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" class="form-control" name="date_to" id="claimed_date_to">
                                    </div>
                                    <div class="col-md-3 d-grid">
                                        <button type="button" class="btn btn-primary" id="generateClaimed">Generate
                                            Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="claimed_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="claimed_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="claimed_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="claimedTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Date Claimed</th>
                                                <th>Owner Name</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Interest Amount</th>
                                                <th>Total Paid</th>
                                                <th>Contact No.</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="6" class="text-end">Totals</th>
                                                <th id="claimed_total_pawned">0.00</th>
                                                <th id="claimed_total_interest">0.00</th>
                                                <th id="claimed_total_paid">0.00</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <!-- Populate via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forfeited Tab -->
                    <div class="tab-pane fade" id="forfeited" role="tabpanel" aria-labelledby="forfeited-tab">
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="forfeitedFilters" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Branch</label>
                                        <select class="form-select" name="branch_id" id="forfeited_branch">
                                            <option value="">All Branches</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date From</label>
                                        <input type="date" class="form-control" name="date_from"
                                            id="forfeited_date_from">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Date To</label>
                                        <input type="date" class="form-control" name="date_to" id="forfeited_date_to">
                                    </div>
                                    <div class="col-md-3 d-grid">
                                        <button type="button" class="btn btn-primary" id="generateForfeited">Generate
                                            Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 d-flex justify-content-end gap-2">
                                    <button class="btn btn-success" id="forfeited_export_excel"><i
                                            class="bi bi-file-earmark-excel"></i> Excel</button>
                                    <button class="btn btn-danger" id="forfeited_export_pdf"><i
                                            class="bi bi-file-earmark-pdf"></i> PDF</button>
                                    <button class="btn btn-secondary" id="forfeited_print"><i class="bi bi-printer"></i>
                                        Print</button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered" id="forfeitedTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date Pawned</th>
                                                <th>Date Forfeited/th>
                                                <th>Owner</th>
                                                <th>Unit</th>
                                                <th>Category</th>
                                                <th>Amount Pawned</th>
                                                <th>Contact No.</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-end">Total</th>
                                                <!-- spans columns before Amount Pawned -->
                                                <th id="forfeited_total_amount">0.00</th>
                                                <!-- aligns with Amount Pawned -->
                                                <th colspan="2"></th> <!-- remaining columns -->
                                            </tr>
                                        </tfoot>

                                        <tbody>
                                            <!-- Populate via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>


<script>



    let pawnedTable;

    $(document).ready(function () {
        pawnedTable = $('#pawnedTable').DataTable({
            ajax: {
                url: '../api/pawn_list.php',
                dataSrc: 'data',
                data: function (d) {
                    d.branch_id = $('#branchFilter').val();
                    d.start_date = $('#fromDate').val();
                    d.end_date = $('#toDate').val();
                }
            },
            columns: [
                { data: 0 }, // auto-number placeholder
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7 },
                { data: 8 }
            ],
            order: [[1, 'desc']], // sort by Date Pawned
            footerCallback: function (row, data, start, end, display) {
                let api = this.api();

                // Remove ₱ and commas to sum the Amount Pawned column (column index 5)
                let intVal = function (i) {
                    return typeof i === 'string'
                        ? i.replace(/[\₱,]/g, '') * 1
                        : typeof i === 'number' ? i : 0;
                };

                let totalAmount = api
                    .column(5, { page: 'current' }) // current page only, change to {} for all filtered
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                $('#pawned_total_amount').html('₱' + totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        });

        // Filter button
        $('#filterBtn').on('click', function () {
            pawnedTable.ajax.reload();
        });

        // Reset button
        $('#resetBtn').on('click', function () {
            $('#branchFilter').val('');
            $('#fromDate').val('');
            $('#toDate').val('');
            pawnedTable.ajax.reload();
        });

        // Auto-number first column
        pawnedTable.on('order.dt search.dt', function () {
            pawnedTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });


    // generate report button
    $('#generatePawned').click(function () {
        pawnedTable.ajax.reload();
    });




    // print pawned table
    $('#pawned_print').on('click', function () {
        // Get filter values
        let branch = $('#branchFilter option:selected').text() || 'All Branches';
        let fromDate = $('#fromDate').val() || 'N/A';
        let toDate = $('#toDate').val() || 'N/A';

        // Get table HTML (with footer)
        let tableHtml = document.getElementById('pawnedTable').outerHTML;

        // Open print window
        let printWindow = window.open('', '', 'width=900,height=700');

        printWindow.document.write(`
        <html>
        <head>
            <title>Pawned Items Report</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                h2, h4 { text-align: center; margin: 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                tfoot th { font-weight: bold; text-align: center; border: none}
                .filters { margin-top: 10px; font-size: 12px; text-align: center; }
            </style>
        </head>
        <body>
            <h2>Pawned Items Report</h2>
            <div class="filters">
                Branch: ${branch} | From: ${fromDate} | To: ${toDate}
            </div>
            ${tableHtml}
        </body>
        </html>
    `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });


    // export pdf pawned items report
    $('#pawned_export_pdf').on('click', function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // landscape, millimeters, A4

        // Filters
        let branch = $('#branchFilter option:selected').text() || 'All Branches';
        let fromDate = $('#fromDate').val() || 'N/A';
        let toDate = $('#toDate').val() || 'N/A';

        // Header
        doc.setFontSize(14);
        doc.text("Pawned Items Report", doc.internal.pageSize.getWidth() / 2, 15, { align: "center" });

        doc.setFontSize(10);
        doc.text(`Branch: ${branch} | From: ${fromDate} | To: ${toDate}`, doc.internal.pageSize.getWidth() / 2, 22, { align: "center" });

        // Extract DataTable
        let data = [];
        let headers = [];
        $('#pawnedTable thead th').each(function () {
            headers.push($(this).text().trim());
        });

        $('#pawnedTable tbody tr').each(function () {
            let row = [];
            $(this).find('td').each(function () {
                row.push($(this).text().trim().replace("₱", "PHP "));
            });
            data.push(row);
        });

        // Footer total
        let footer = [];
        $('#pawnedTable tfoot th').each(function () {
            footer.push($(this).text().trim().replace("₱", "PHP "));
        });

        let totalText = document.getElementById("pawned_total_amount").innerText;

        // Replace ₱ with PHP
        totalText = totalText.replace("₱", "PHP ");

        // AutoTable with footer
        doc.autoTable({
            head: [headers],
            body: data,
            foot: [[
                { content: 'Total Pawned Amount', colSpan: 5, styles: { halign: 'right' } },  // span across first 5 columns
               { content: totalText, styles: { halign: 'left' } },
                // Notes
            ]],
            startY: 30,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [52, 58, 64] }, // dark gray header
            footStyles: { fillColor: [52, 58, 64], fontStyle: 'bold' }
        });


        // Save PDF
        doc.save('pawned-items-report.pdf');
    });


    // export pawned items report excel
document.getElementById("pawned_export_excel").addEventListener("click", function () {
    // Get DataTable instance
    let table = $('#pawnedTable').DataTable();
    let data = table.rows({ search: 'applied' }).data().toArray();

    // Extract headers
    let headers = [];
    $('#pawnedTable thead th').each(function () {
        headers.push($(this).text().trim());
    });

    // Build worksheet data
    let ws_data = [headers];
    data.forEach(function (row) {
        ws_data.push(row);
    });

    // Add footer total
    let totalText = document.getElementById("pawned_total_amount").innerText;
    totalText = totalText.replace("₱", "PHP ");
    ws_data.push(["", "", "", "", "Total", totalText]);

    // Create worksheet & workbook
    let ws = XLSX.utils.aoa_to_sheet(ws_data);

    // Apply borders to all cells
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let R = range.s.r; R <= range.e.r; ++R) {
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let cell_address = { c: C, r: R };
            let cell_ref = XLSX.utils.encode_cell(cell_address);

            if (!ws[cell_ref]) continue;

            if (!ws[cell_ref].s) ws[cell_ref].s = {};
            ws[cell_ref].s.border = {
                top:    { style: "thin", color: { auto: 1 } },
                right:  { style: "thin", color: { auto: 1 } },
                bottom: { style: "thin", color: { auto: 1 } },
                left:   { style: "thin", color: { auto: 1 } }
            };
        }
    }

    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Pawned Items");

    // Export file
    XLSX.writeFile(wb, "pawned_items.xlsx");
});


</script>