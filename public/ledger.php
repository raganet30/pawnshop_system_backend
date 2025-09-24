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
                <h2>Cash Ledger</h2>
            </div>


            <!-- Date & Transaction Filters -->
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

                <!-- TXN Type filter (from DB) -->
                <div class="col-md-3">
                    <label for="txnTypeFilter" class="form-label">Transaction Type:</label>
                    <select id="txnTypeFilter" class="form-select">
                        <option value="">All Types</option>
                        <?php
                        $stmt = $pdo->query("SELECT DISTINCT txn_type FROM cash_ledger ORDER BY txn_type ASC");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($row['txn_type']) . '">'
                                . htmlspecialchars(ucwords(str_replace('_', ' ', $row['txn_type'])))
                                . '</option>';
                        }
                        ?>
                    </select>
                </div>


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


            <!-- DataTable -->
            <div class="card">
                <div class="card-header">Ledger Records
                </div>
                <div class="card-body">
                    <!-- Export Options -->
                    <div class="mb-3 d-flex justify-content-end gap-2">
                        <button class="btn btn-success" id="cashLedgerExcel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                        <button class="btn btn-danger" id="cashLedgerPdf">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                        <button class="btn btn-secondary" id="cashLedgerPrint">
                            <i class="bi bi-printer"></i> Print
                        </button>
                    </div>


                    <table id="cashLedgerTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Txn Type</th>
                                <th>Direction</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated dynamically via DataTables AJAX -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end"></th>
                                <th colspan="2" class="text-success"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL IN:</th>
                                <th colspan="2" id="totalIn" class="text-success"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL OUT:</th>
                                <th colspan="2" id="totalOut" class="text-danger"></th>
                                <th colspan="4"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">BALANCE:</th>
                                <th colspan="2" id="balance" class="text-primary"></th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


        </div>

        <?php include '../views/footer.php'; ?>
    </div>
</div>




<script>
    $(document).ready(function () {

        let table = $('#cashLedgerTable').DataTable({
            columnDefs: [{ className: "text-center", targets: "_all" }],
            ajax: {
                url: "../api/cash_ledger_list.php",
                data: function (d) {
                    d.fromDate = $('#fromDate').val();
                    d.toDate = $('#toDate').val();
                    d.txn_type = $('#txnTypeFilter').val();
                    <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                        d.branch_id = $('#branchFilter').val();
                    <?php endif; ?>
                }
            },
            columns: [
                {
                    title: "#",
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    className: "text-center"
                },
                { title: "Date", data: 1 },
                {
                    title: "Branch",
                    data: 2,
                    visible: <?php echo $_SESSION['user']['role'] === 'super_admin' ? 'true' : 'false'; ?>
                },
                { title: "Txn Type", data: 3 },
                {
                    title: "Direction",
                    data: 4,
                    render: function (data) {
                        return data.toLowerCase() === 'in'
                            ? '<span class="badge bg-success">IN</span>'
                            : '<span class="badge bg-danger">OUT</span>';
                    }
                },
                { title: "Amount", data: 5 },
                { title: "Reference", data: 6 },
                { title: "Description", data: 7 },
                { title: "User", data: 8 }
            ],
            footerCallback: function (row, data) {
                let intVal = i => typeof i === 'string' ? i.replace(/[\₱,]/g, '') * 1 : i || 0;
                let totalIn = 0, totalOut = 0;

                data.forEach(row => {
                    let direction = row[4].toLowerCase();
                    let amount = intVal(row[5]);
                    if (direction === "in") totalIn += amount;
                    else if (direction === "out") totalOut += amount;
                });

                $('#totalIn').html("₱" + totalIn.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $('#totalOut').html("₱" + totalOut.toLocaleString(undefined, { minimumFractionDigits: 2 }));
                $('#balance').html("₱" + (totalIn - totalOut).toLocaleString(undefined, { minimumFractionDigits: 2 }));
            }
        });

        <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
            table.ajax.reload();
            $('#branchFilter').on('change', function () {
                table.ajax.reload();
            });
        <?php endif; ?>

        $('#filterBtn').on('click', function () { table.ajax.reload(); });
        $('#resetBtn').on('click', function () {
            $('#fromDate').val('');
            $('#toDate').val('');
            $('#txnTypeFilter').val('');
            table.ajax.reload();
        });



        // --- Export Excel Cash Ledger Report ---
        $('#cashLedgerExcel').on('click', function () {
            let data = table.rows({ search: 'applied' }).data().toArray();

            // Extract headers
            let headers = [];
            $('#cashLedgerTable thead th').each(function () {
                headers.push($(this).text().trim());
            });

            // Map rows, convert Amount to numeric
            let rows = data.map((row, i) => [
                i + 1,
                row[1], // Date
                row[2], // Branch
                row[3], // Txn Type
                row[4], // Direction
                parseFloat(row[5].toString().replace(/[\₱,]/g, '')), // Amount numeric
                row[6], row[7], row[8]
            ]);

            // Compute totals
            let totalIn = 0, totalOut = 0;
            rows.forEach(r => {
                if (r[4].toLowerCase() === 'in') totalIn += r[5];
                else if (r[4].toLowerCase() === 'out') totalOut += r[5];
            });
            let balance = totalIn - totalOut;

            // Append totals rows
            let blankRow = new Array(headers.length).fill("");
            let totalInRow = [...blankRow];
            totalInRow[3] = "TOTAL IN";
            totalInRow[5] = totalIn.toFixed(2);

            let totalOutRow = [...blankRow];
            totalOutRow[3] = "TOTAL OUT";
            totalOutRow[5] = totalOut.toFixed(2);

            let balanceRow = [...blankRow];
            balanceRow[3] = "BALANCE";
            balanceRow[5] = balance.toFixed(2);

            rows.push(totalInRow, totalOutRow, balanceRow);

            // Add branch and date info as first rows
            let branchName = "<?php echo $_SESSION['user']['branch_name'] ?? 'ALL'; ?>";
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                let selectedBranchName = $('#branchFilter option:selected').text() || 'ALL';
            <?php endif; ?>
            let headerInfo = [
                ["Branch:", <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>selectedBranchName<?php else: ?>branchName<?php endif; ?>],
                ["Date From:", $('#fromDate').val() || 'ALL', "To:", $('#toDate').val() || 'ALL'],
                []
            ];

            let ws_data = [...headerInfo, headers, ...rows];

            // Create workbook & worksheet
            let wb = XLSX.utils.book_new();
            let ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Cash Ledger");

            let fromDate = $('#fromDate').val() || 'ALL';
            let toDate = $('#toDate').val() || 'ALL';
            XLSX.writeFile(wb, `cash_ledger_${fromDate}_${toDate}.xlsx`);
        });



        // --- Export PDF ---
        $('#cashLedgerPdf').on('click', function () {
            let { jsPDF } = window.jspdf;
            let doc = new jsPDF('l', 'pt', 'a4');

            // Extract headers
            let headers = [];
            $('#cashLedgerTable thead th').each(function () {
                headers.push($(this).text().trim());
            });

            // Map rows and convert Amount to numeric
            let data = table.rows({ search: 'applied' }).data().toArray().map((row, i) => [
                i + 1,
                row[1],           // Date
                row[2],           // Branch
                row[3],           // Txn Type
                row[4],           // Direction
                parseFloat(row[5].replace(/[\₱,]/g, '')), // Amount numeric
                row[6], row[7], row[8]
            ]);

            // Compute totals
            let totalIn = 0, totalOut = 0;
            data.forEach(r => {
                if (r[4].toLowerCase() === 'in') totalIn += r[5];
                else if (r[4].toLowerCase() === 'out') totalOut += r[5];
            });
            let balance = totalIn - totalOut;

            // Append footer totals
            let blankRow = new Array(headers.length).fill("");
            let totalInRow = [...blankRow]; totalInRow[3] = "TOTAL IN"; totalInRow[5] = totalIn;
            let totalOutRow = [...blankRow]; totalOutRow[3] = "TOTAL OUT"; totalOutRow[5] = totalOut;
            let balanceRow = [...blankRow]; balanceRow[3] = "BALANCE"; balanceRow[5] = balance;
            data.push(totalInRow, totalOutRow, balanceRow);

            // Header info
            let branchName = "<?php echo $_SESSION['user']['branch_name'] ?? 'ALL'; ?>";
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                let selectedBranchName = $('#branchFilter option:selected').text() || 'ALL';
            <?php endif; ?>

            doc.text("Cash Ledger Report", 40, 40);
            doc.text("Branch: <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>" + selectedBranchName + "<?php else: ?>" + branchName + "<?php endif; ?>", 40, 60);
            doc.text("Date From: " + ($('#fromDate').val() || 'ALL') + " To: " + ($('#toDate').val() || 'ALL'), 40, 80);

            doc.autoTable({
                head: [headers],
                body: data,
                startY: 100,
                styles: { fontSize: 8 },
                didParseCell: function (cellData) {
                    // Add peso sign to Amount column
                    if (cellData.column === 5) {
                        let num = parseFloat(cellData.cell.text) || 0;
                        cellData.cell.text = "₱" + num.toLocaleString(undefined, { minimumFractionDigits: 2 });
                    }
                }
            });

            let fromDate = $('#fromDate').val() || 'ALL';
            let toDate = $('#toDate').val() || 'ALL';
            doc.save(`cash_ledger_${fromDate}_${toDate}.pdf`);
        });



        // --- Print Cash Ledger Report ---
        $('#cashLedgerPrint').on('click', function () {
            let headers = [];
            $('#cashLedgerTable thead th').each(function () {
                headers.push($(this).text().trim());
            });

            // Prepare rows
            let data = table.rows({ search: 'applied' }).data().toArray().map((row, i) => [
                i + 1,
                row[1], // Date
                row[2], // Branch
                row[3], // Txn Type
                row[4], // Direction
                "₱" + parseFloat(row[5].replace(/[\₱,]/g, '')).toLocaleString(undefined, { minimumFractionDigits: 2 }),
                row[6], row[7], row[8]
            ]);

            // Compute totals
            let totalIn = 0, totalOut = 0;
            data.forEach(r => {
                let val = parseFloat(r[5].replace(/[\₱,]/g, ''));
                if (r[4].toLowerCase() === 'in') totalIn += val;
                else if (r[4].toLowerCase() === 'out') totalOut += val;
            });
            let balance = totalIn - totalOut;

            // Append footer totals aligned under Amount column
            let blankRow = new Array(headers.length).fill("");
            let totalInRow = [...blankRow]; totalInRow[3] = "TOTAL IN"; totalInRow[5] = "₱" + totalIn.toLocaleString(undefined, { minimumFractionDigits: 2 });
            let totalOutRow = [...blankRow]; totalOutRow[3] = "TOTAL OUT"; totalOutRow[5] = "₱" + totalOut.toLocaleString(undefined, { minimumFractionDigits: 2 });
            let balanceRow = [...blankRow]; balanceRow[3] = "BALANCE"; balanceRow[5] = "₱" + balance.toLocaleString(undefined, { minimumFractionDigits: 2 });

            data.push(totalInRow, totalOutRow, balanceRow);

            // Open print window
            let printWindow = window.open('', '', 'width=1200,height=700');
            printWindow.document.write('<html><head><title>Cash Ledger</title></head><body>');
            printWindow.document.write('<h3>Cash Ledger Report</h3>');

            // Branch & Date info
            let branchName = "<?php echo $_SESSION['user']['branch_name'] ?? 'ALL'; ?>";
            <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>
                let selectedBranchName = $('#branchFilter option:selected').text() || 'ALL';
            <?php endif; ?>
            printWindow.document.write('<p>Branch: <?php if ($_SESSION['user']['role'] === 'super_admin'): ?>' + selectedBranchName + '<?php else: ?>' + branchName + '<?php endif; ?> | Date From: ' + ($('#fromDate').val() || 'ALL') + ' To: ' + ($('#toDate').val() || 'ALL') + '</p>');

            // Table
            printWindow.document.write('<table border="1" cellspacing="0" cellpadding="5"><thead><tr>');
            headers.forEach(h => printWindow.document.write('<th>' + h + '</th>'));
            printWindow.document.write('</tr></thead><tbody>');
            data.forEach((r, index) => {
                let isFooter = index >= data.length - 3; // last 3 rows are totals
                printWindow.document.write('<tr>');
                r.forEach(c => {
                    if (isFooter) {
                        printWindow.document.write('<td style="border:none">' + c + '</td>');
                    } else {
                        printWindow.document.write('<td>' + c + '</td>');
                    }
                });
                printWindow.document.write('</tr>');
            });
            printWindow.document.write('</tbody></table></body></html>');
            printWindow.document.close();
            printWindow.print();
        });

    });


</script>