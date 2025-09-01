$(document).ready(function () {

    // --- Initialize Forfeited DataTable ---
    let forfeitedTable = $('#forfeitedTable').DataTable({
        processing: true,
        serverSide: false, // API returns all filtered data
        ajax: {
            url: '../api/forfeit_list.php',
            data: function (d) {
                d.branch_id = $('#forfeited_branch_filter').val();
                d.fromDate = $('#forfeited_from_date').val();
                d.toDate = $('#forfeited_to_date').val();
            }
        },
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 }, // auto-number
            { data: 1 }, // date_pawned
            { data: 2 }, // date_forfeited
            { data: 3 }, // owner_name
            { data: 4 }, // unit_description
            { data: 5 }, // category
            { data: 6 }, // amount_pawned
            { data: 7 }, // contact_no
            { data: 8 }, // reason
        ],
        order: [[2, 'desc']], // sort by Date Forfeited
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            // Helper: remove currency and commas
            let parseVal = function (val) {
                return typeof val === 'string' ? parseFloat(val.replace(/[₱,]/g, '')) || 0
                    : typeof val === 'number' ? val : 0;
            };

            let totalAmount = 0;

            data.forEach(row => {
                totalAmount += parseVal(row[6]);
            });

            $('#forfeited_total_amount').html(totalAmount.toFixed(2));
        }
    });

    // --- Filter & Reset ---
    $('#forfeited_filter_btn').on('click', function () {
        forfeitedTable.ajax.reload();
    });

    $('#forfeited_reset_btn').on('click', function () {
        $('#forfeited_branch_filter').val('');
        $('#forfeited_from_date').val('');
        $('#forfeited_to_date').val('');
        forfeitedTable.ajax.reload();
    });



    // --- Print Forfeited Items ---
    document.getElementById("forfeited_print").addEventListener("click", function () {
        let headers = [];
        $('#forfeitedTable thead th').each(function () {
            headers.push($(this).text().trim());
        });

        // Get filtered table data and format Amount Pawned with Peso sign
        let data = forfeitedTable.rows({ search: 'applied' }).data().toArray().map((row, i) => {
            let cleanAmount = parseFloat(String(row[6]).replace(/[^0-9.-]+/g, '')) || 0;
            return [
                i + 1,
                row[1], // Date Pawned
                row[2], // Date Forfeited
                row[3], // Owner
                row[4], // Unit
                row[5], // Category
                '₱' + cleanAmount.toLocaleString(undefined, { minimumFractionDigits: 2 }), // Amount Pawned formatted
                row[7], // Contact No.
                row[8]  // Reason
            ];
        });

        // Calculate total Amount Pawned
        let totalAmount = data.reduce((sum, row) => {
            let val = parseFloat(String(row[6]).replace(/[^0-9.-]+/g, '')) || 0;
            return sum + val;
        }, 0);

        // Append totals row
        data.push([
            '', '', '', '', '', 'Total', '₱' + totalAmount.toLocaleString(undefined, { minimumFractionDigits: 2 }), '', ''
        ]);

        // Open print window
        let printWindow = window.open('', '', 'width=1200,height=700');
        printWindow.document.write('<html><head><title>Forfeited Items Report</title></head><body>');
        printWindow.document.write('<h3>Forfeited Items Report</h3>');

        printWindow.document.write('<table border="1" cellspacing="0" cellpadding="5"><thead><tr>');
        headers.forEach(h => printWindow.document.write('<th>' + h + '</th>'));
        printWindow.document.write('</tr></thead><tbody>');

        data.forEach(row => {
            printWindow.document.write('<tr>');
            row.forEach(col => printWindow.document.write('<td>' + col + '</td>'));
            printWindow.document.write('</tr>');
        });

        printWindow.document.write('</tbody></table>');
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });




    // --- Export Forfeited Items PDF ---
    document.getElementById("forfeited_export_pdf").addEventListener("click", function () {
        let { jsPDF } = window.jspdf;
        let doc = new jsPDF('l', 'pt', 'a4'); // landscape orientation

        // Collect headers
        let headers = [];
        $('#forfeitedTable thead th').each(function () {
            headers.push($(this).text().trim());
        });

        // Get filtered rows and clean Amount Pawned column
        let data = forfeitedTable.rows({ search: 'applied' }).data().toArray().map((row, i) => {
            let cleanAmount = String(row[6]).replace(/[^0-9.-]+/g, ''); // remove ₱, commas, ±, etc.
            return [
                i + 1,
                row[1], // Date Pawned
                row[2], // Date Forfeited
                row[3], // Owner
                row[4], // Unit
                row[5], // Category
                parseFloat(cleanAmount).toFixed(2), // Amount Pawned numeric
                row[7], // Contact No.
                row[8]  // Reason
            ];
        });

        // Calculate total Amount Pawned
        let totalAmount = data.reduce((sum, row) => sum + parseFloat(row[6]), 0);

        // Append totals row
        data.push([
            '', '', '', '', '', 'Total', totalAmount.toFixed(2), '', ''
        ]);

        // Add title
        doc.text("Forfeited Items Report", 40, 40);

        // Generate table
        doc.autoTable({
            head: [headers],
            body: data,
            startY: 60,
            styles: { fontSize: 8 }
        });

        // Save file
        doc.save("forfeited_items_report.pdf");
    });


    // --- Export Forfeited Items Excel ---
// --- Export Forfeited Items Excel ---
document.getElementById("forfeited_export_excel").addEventListener("click", function () {
    let data = forfeitedTable.rows({ search: 'applied' }).data().toArray();

    // Extract headers
    let headers = [];
    $('#forfeitedTable thead th').each(function () {
        headers.push($(this).text().trim());
    });

    // Build worksheet data
    let ws_data = [headers];
    let totalAmount = 0;

    data.forEach((row, i) => {
        let cleanAmount = parseFloat(String(row[6]).replace(/[^0-9.-]+/g, '')) || 0;
        totalAmount += cleanAmount;

        ws_data.push([
            i + 1,
            row[1], // Date Pawned
            row[2], // Date Forfeited
            row[3], // Owner
            row[4], // Unit
            row[5], // Category
            cleanAmount.toFixed(2), // Amount Pawned numeric
            row[7], // Contact No.
            row[8]  // Reason
        ]);
    });

    // Append totals row aligned with Amount Pawned column
    let footerRow = new Array(headers.length).fill('');
    footerRow[5] = 'Total'; // label under Amount Pawned
    footerRow[6] = totalAmount.toFixed(2); // numeric total
    ws_data.push(footerRow);

    // Create worksheet & workbook
    let ws = XLSX.utils.aoa_to_sheet(ws_data);

    // Optional: add thin borders to all cells
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let R = range.s.r; R <= range.e.r; ++R) {
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let cell_address = { c: C, r: R };
            let cell_ref = XLSX.utils.encode_cell(cell_address);
            if (!ws[cell_ref]) continue;
            if (!ws[cell_ref].s) ws[cell_ref].s = {};
            ws[cell_ref].s.border = {
                top: { style: "thin", color: { auto: 1 } },
                right: { style: "thin", color: { auto: 1 } },
                bottom: { style: "thin", color: { auto: 1 } },
                left: { style: "thin", color: { auto: 1 } }
            };
        }
    }

    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Forfeited Items");

    // Filename with date filters
    let fromDate = document.getElementById("forfeited_from_date").value || "ALL";
    let toDate = document.getElementById("forfeited_to_date").value || "ALL";
    let filename = `forfeited_report_${fromDate}_${toDate}.xlsx`;

    XLSX.writeFile(wb, filename);
});




});



