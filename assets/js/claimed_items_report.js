$(document).ready(function () {
    let claimedTable = $('#claimedTable').DataTable({
        processing: true,
        serverSide: false, // API already applies filtering
        ajax: {
            url: '../api/claim_list.php',
            data: function (d) {
                d.branch_id = $('#claimed_branchFilter').val();
                d.start_date = $('#claimed_fromDate').val();
                d.end_date = $('#claimed_toDate').val();
            }
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 }, // index
            { data: 0 }, // date_pawned
            { data: 1 }, // date_claimed
            { data: 2 }, // owner_name
            { data: 3 }, // unit_description
            { data: 4 }, // category
            { data: 5 }, // amount_pawned
            { data: 6 }, // interest_amount
            { data: 7 }, // total_paid
            { data: 8 }, // contact_no
        ],
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            // Helper: strip currency & commas
            let parseVal = function (val) {
                return typeof val === 'string'
                    ? Number(val.replace(/[₱,]/g, '')) || 0
                    : typeof val === 'number'
                        ? val
                        : 0;
            };

            let pawned = 0, interest = 0, paid = 0;

            data.forEach(row => {
                pawned += parseVal(row[5]);
                interest += parseVal(row[6]);
                paid += parseVal(row[7]);
            });

            $('#claimed_total_pawned').html('₱' + pawned.toLocaleString(undefined, { minimumFractionDigits: 2 }));
            $('#claimed_total_interest').html('₱' + interest.toLocaleString(undefined, { minimumFractionDigits: 2 }));
            $('#claimed_total_paid').html('₱' + paid.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        }
    });

    // --- Filter & Reset ---
    $('#claimed_filterBtn').on('click', function () {
        claimedTable.ajax.reload();
    });

    $('#claimed_resetBtn').on('click', function () {
        $('#claimed_branchFilter').val('');
        $('#claimed_fromDate').val('');
        $('#claimed_toDate').val('');
        claimedTable.ajax.reload();
    });


  // --- Print Claimed Items Report---
document.getElementById("claimed_print").addEventListener("click", function () {
    let headers = [];
    $('#claimedTable thead th').each(function () {
        headers.push($(this).text().trim());
    });

    let data = claimedTable.rows({ search: 'applied' }).data().toArray().map((row, i) => {
        return [
            i + 1,
            row[0],
            row[1],
            row[2],
            row[3],
            row[4],
            row[5],
            row[6],
            row[7],
            row[8]
        ];
    });

    // Grab totals from your table footer
    let totalPawned   = $('#claimed_total_pawned').text();
    let totalInterest = $('#claimed_total_interest').text();
    let totalPaid     = $('#claimed_total_paid').text();

    let printWindow = window.open('', '', 'width=1200,height=700');
    printWindow.document.write('<html><head><title>Claims Report</title></head><body>');
    printWindow.document.write('<h3>Claims Report</h3>');
    printWindow.document.write('<table border="1" cellspacing="0" cellpadding="5"><thead><tr>');
    headers.forEach(h => printWindow.document.write('<th>' + h + '</th>'));
    printWindow.document.write('</tr></thead><tbody>');

    data.forEach(row => {
        printWindow.document.write('<tr>');
        row.forEach(col => printWindow.document.write('<td>' + col + '</td>'));
        printWindow.document.write('</tr>');
    });

    printWindow.document.write('</tbody>');

    // Add totals row
    printWindow.document.write('<tfoot><tr>');
    printWindow.document.write('<td colspan="6" style="text-align:right;"><strong>Totals</strong></td>');
    printWindow.document.write('<td><strong>' + totalPawned + '</strong></td>');
    printWindow.document.write('<td><strong>' + totalInterest + '</strong></td>');
    printWindow.document.write('<td><strong>' + totalPaid + '</strong></td>');
    printWindow.document.write('<td></td>'); // leave last column blank if you have actions/etc.
    printWindow.document.write('</tr></tfoot>');

    printWindow.document.write('</table>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
});




    // --- Export PDF Claimed Items Report ---
document.getElementById("claimed_export_pdf").addEventListener("click", function () {
    let { jsPDF } = window.jspdf;
    let doc = new jsPDF('l', 'pt', 'a4');

    let headers = [];
    $('#claimedTable thead th').each(function () {
        headers.push($(this).text().trim());
    });

    // Helper: clean values (remove ₱, ±, hidden chars) and prefix with PHP for money columns
    let cleanValue = (val, isMoney = false) => {
        if (!val) return "";
        let cleaned = String(val).replace(/[^\d.,-]/g, ''); // keep only numbers, commas, dot, minus
        return isMoney ? "PHP " + cleaned : cleaned;
    };

    let data = claimedTable.rows({ search: 'applied' }).data().toArray().map((row, i) => {
        return [
            i + 1,
            cleanValue(row[0]),
            cleanValue(row[1]),
            cleanValue(row[2]),
            cleanValue(row[3]),
            cleanValue(row[4]),
            cleanValue(row[5], true), // Pawned Amount
            cleanValue(row[6], true), // Interest
            cleanValue(row[7], true), // Paid
            cleanValue(row[8])
        ];
    });

    // Clean footer values
    let totalPawned   = cleanValue($('#claimed_total_pawned').text(), true);
    let totalInterest = cleanValue($('#claimed_total_interest').text(), true);
    let totalPaid     = cleanValue($('#claimed_total_paid').text(), true);

    let totalsRow = [
        "", "", "", "", "", "Totals",
        totalPawned,
        totalInterest,
        totalPaid,
        ""
    ];

    doc.text("Claims Report", 40, 40);

    doc.autoTable({
        head: [headers],
        body: [...data, totalsRow],
        startY: 60,
        styles: { fontSize: 8 },
        columnStyles: {
            6: { halign: 'right' },
            7: { halign: 'right' },
            8: { halign: 'right' }
        }
    });

    doc.save("claims_report.pdf");
});


// --- Export Excel Claimed Items Report ---
document.getElementById("claimed_export_excel").addEventListener("click", function () {
    let data = claimedTable.rows({ search: 'applied' }).data().toArray();

    let headers = [];
    $('#claimedTable thead th').each(function () {
        headers.push($(this).text().trim());
    });

    // Helper to clean and format money values with 2 decimals
    function formatMoney(val) {
        if (!val) return 0.00;
        let num = parseFloat(String(val).replace(/[^0-9.-]+/g, "")) || 0;
        return parseFloat(num.toFixed(2));
    }

    // Build rows
    let rows = data.map((row, i) => {
        return [
            i + 1,                   // #
            row[0],                  // Date Pawned
            row[1],                  // Date Claimed
            row[2],                  // Owner Name
            row[3],                  // Unit
            row[4],                  // Category
            formatMoney(row[5]),     // Amount Pawned (2 decimals)
            formatMoney(row[6]),     // Interest Amount (2 decimals)
            formatMoney(row[7]),     // Total Paid (2 decimals)
            row[8]                   // Contact No.
        ];
    });

    // Calculate totals
    let totalPawned = data.reduce((sum, r) => sum + formatMoney(r[5]), 0).toFixed(2);
    let totalInterest = data.reduce((sum, r) => sum + formatMoney(r[6]), 0).toFixed(2);
    let totalPaid = data.reduce((sum, r) => sum + formatMoney(r[7]), 0).toFixed(2);

    // Footer row
    let footerRow = [
        "", "", "", "", "", "Totals",
        parseFloat(totalPawned),
        parseFloat(totalInterest),
        parseFloat(totalPaid),
        ""
    ];

    let ws_data = [headers, ...rows, footerRow];
    let wb = XLSX.utils.book_new();
    let ws = XLSX.utils.aoa_to_sheet(ws_data);

    // Bold totals row
    let footerCellRange = XLSX.utils.decode_range(ws['!ref']);
    let lastRow = footerCellRange.e.r; // index of last row
    for (let c = 0; c <= 9; c++) {
        let cellRef = XLSX.utils.encode_cell({ r: lastRow, c });
        if (ws[cellRef]) {
            ws[cellRef].s = { font: { bold: true } };
        }
    }

    XLSX.utils.book_append_sheet(wb, ws, "Claims Report");
    XLSX.writeFile(wb, "claims_report.xlsx");
});





});


