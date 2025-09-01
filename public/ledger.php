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


            <?php include '../views/filters.php'; ?>

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
        table.ajax.reload();
    });

    // --- Export Excel ---
    $('#cashLedgerExcel').on('click', function () {
        let data = table.rows({ search: 'applied' }).data().toArray();
        let headers = [];
        $('#cashLedgerTable thead th').each(function () { headers.push($(this).text().trim()); });
        
        let rows = data.map((row, i) => [
            i + 1,
            row[1], // Date
            row[2], // Branch
            row[3], // Txn Type
            row[4], // Direction
            parseFloat(row[5].replace(/[\₱,]/g,'')), // Amount numeric
            row[6], row[7], row[8]
        ]);

        // Add footer totals
        let totalIn = 0, totalOut = 0;
        rows.forEach(r => {
            if(r[4].toLowerCase() === 'in') totalIn += r[5];
            else if(r[4].toLowerCase() === 'out') totalOut += r[5];
        });
        let balance = totalIn - totalOut;
        rows.push(["","","","TOTAL IN","","",totalIn,"","",""]);
        rows.push(["","","","TOTAL OUT","","",totalOut,"","",""]);
        rows.push(["","","","BALANCE","","",balance,"","",""]);

        let ws_data = [headers, ...rows];
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
        let headers = [];
        $('#cashLedgerTable thead th').each(function () { headers.push($(this).text().trim()); });

        let data = table.rows({ search: 'applied' }).data().toArray().map((row,i)=>[
            i+1,
            row[1],
            row[2],
            row[3],
            row[4],
            parseFloat(row[5].replace(/[\₱,]/g,'')), // numeric
            row[6], row[7], row[8]
        ]);

        // Footer totals
        let totalIn=0, totalOut=0;
        data.forEach(r=>{
            if(r[4].toLowerCase()==='in') totalIn+=r[5];
            else if(r[4].toLowerCase()==='out') totalOut+=r[5];
        });
        let balance = totalIn-totalOut;
        data.push(["","","","TOTAL IN","",totalIn,"","",""]);
        data.push(["","","","TOTAL OUT","",totalOut,"","",""]);
        data.push(["","","","BALANCE","",balance,"","",""]);

        doc.text("Cash Ledger Report", 40, 40);
        doc.text("Branch: <?php echo $_SESSION['user']['branch_name'] ?? 'ALL'; ?>", 40, 60);
        doc.text("Date From: " + ($('#fromDate').val() || 'ALL') + " To: " + ($('#toDate').val() || 'ALL'), 40, 80);

        doc.autoTable({
            head: [headers],
            body: data,
            startY: 100,
            styles: { fontSize: 8 },
            didParseCell: function (data) {
                // Add peso sign to Amount column and totals
                if([5].includes(data.column)) {
                    data.cell.text = "₱" + parseFloat(data.cell.text).toLocaleString(undefined,{minimumFractionDigits:2});
                }
            }
        });

        doc.save(`cash_ledger_${$('#fromDate').val() || 'ALL'}_${$('#toDate').val() || 'ALL'}.pdf`);
    });

    // --- Print ---
    $('#cashLedgerPrint').on('click', function () {
        let headers = [];
        $('#cashLedgerTable thead th').each(function () { headers.push($(this).text().trim()); });

        let data = table.rows({ search: 'applied' }).data().toArray().map((row,i)=>[
            i+1,
            row[1],
            row[2],
            row[3],
            row[4],
            "₱"+parseFloat(row[5].replace(/[\₱,]/g,'')).toLocaleString(undefined,{minimumFractionDigits:2}),
            row[6], row[7], row[8]
        ]);

        // Footer totals
        let totalIn=0, totalOut=0;
        data.forEach(r=>{
            let val = parseFloat(r[5].replace(/[\₱,]/g,''));
            if(r[4].toLowerCase()==='in') totalIn+=val;
            else if(r[4].toLowerCase()==='out') totalOut+=val;
        });
        let balance = totalIn-totalOut;
        data.push(["","","","TOTAL IN","","₱"+totalIn.toLocaleString(undefined,{minimumFractionDigits:2}), "","",""]);
        data.push(["","","","TOTAL OUT","","₱"+totalOut.toLocaleString(undefined,{minimumFractionDigits:2}), "","",""]);
        data.push(["","","","BALANCE","","₱"+balance.toLocaleString(undefined,{minimumFractionDigits:2}), "","",""]);

        let printWindow = window.open('', '', 'width=1200,height=700');
        printWindow.document.write('<html><head><title>Cash Ledger</title></head><body>');
        printWindow.document.write('<h3>Cash Ledger Report</h3>');
        printWindow.document.write('<p>Branch: <?php echo $_SESSION['user']['branch_name'] ?? "ALL"; ?> | Date From: ' + ($('#fromDate').val() || 'ALL') + ' To: ' + ($('#toDate').val() || 'ALL') + '</p>');
        printWindow.document.write('<table border="1" cellspacing="0" cellpadding="5"><thead><tr>');
        headers.forEach(h=>printWindow.document.write('<th>'+h+'</th>'));
        printWindow.document.write('</tr></thead><tbody>');
        data.forEach(r=>{
            printWindow.document.write('<tr>');
            r.forEach(c=>printWindow.document.write('<td>'+c+'</td>'));
            printWindow.document.write('</tr>');
        });
        printWindow.document.write('</tbody></table></body></html>');
        printWindow.document.close();
        printWindow.print();
    });

});


</script>