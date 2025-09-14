//get user branch from SESSION

let pawnedTable;
// pawned items datatable initialization
$(document).ready(function () {
    pawnedTable = $('#pawnedTable').DataTable({
        ajax: {
            url: '../api/pawn_list.php',
            dataSrc: 'data',
            data: function (d) {
                d.branch_id = $('#pawned_branchFilter').val();
                d.start_date = $('#pawned_fromDate').val();
                d.end_date = $('#pawned_toDate').val();
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
            { data: 8 },
            { data: 9 }
        ],
        order: [[1, 'desc']], // sort by Date Pawned
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            // Remove ₱ and commas to sum the Amount Pawned column (column index 5)
            let intVal = function (i) {
                return typeof i === 'string'
                    ? i.replace(/[₱,]/g, '') * 1
                    : typeof i === 'number' ? i : 0;
            };

            let totalAmount = api
                .column(6, { page: 'current' }) // current page only
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            $('#pawned_total_amount').html(
                '₱' + totalAmount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );
        }
    });

    // --- Filter button ---
    $('#pawned_filterBtn').on('click', function () {
        pawnedTable.ajax.reload();
    });

    // --- Reset button ---
    $('#pawned_resetBtn').on('click', function () {
        $('#pawned_branchFilter').val('');
        $('#pawned_fromDate').val('');
        $('#pawned_toDate').val('');
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


// print pawned items table
$('#pawned_print').on('click', function () {

    let branch, fromDate, toDate;
    fromDate = $('#fromDate').val() || 'N/A';
    toDate = $('#toDate').val() || 'N/A';

    if (userRole === 'super_admin') {
        // Super admin → use dropdown or default to "All Branches"
        branch = $('#branchFilter option:selected').text() || 'All Branches';
    } else {
        // Admin/cashier → always their assigned branch
        branch = userBranch || 'My Branch';
    }

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
                tfoot th { font-weight: bold; text-align: center; border: none }
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
    let branch, fromDate, toDate;
    fromDate = $('#pawned_fromDate').val() || 'N/A';
    toDate = $('#pawned_toDate').val() || 'N/A';

    if (userRole === 'super_admin') {
        // Super admin → use dropdown or default to "All Branches"
        branch = $('#pawned_branchFilter option:selected').text() || 'All Branches';
    } else {
        // Admin/cashier → always their assigned branch
        branch = userBranch || 'My Branch';
    }
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
        styles: { fontSize: 8 }
    });


    // Save PDF
    doc.save('pawned-items-report.pdf');
});



// --- Export Pawned Items Report Excel ---
document.getElementById("pawned_export_excel").addEventListener("click", function () {
    // Get DataTable instance
    let table = $('#pawnedTable').DataTable();
    let data = table.rows({ search: 'applied' }).data().toArray();

    // Extract headers (exclude first and last column)
    let headers = [];
    $('#pawnedTable thead th').each(function (index) {
        if (index > 0 && index < $('#pawnedTable thead th').length - 1) {
            headers.push($(this).text().trim());
        }
    });

    // Helper: parse money → numeric with 2 decimals
    function formatMoney(val) {
        if (!val) return 0.00;
        let num = parseFloat(String(val).replace(/[^0-9.-]+/g, "")) || 0;
        return parseFloat(num.toFixed(2));
    }

    // Build worksheet rows (exclude first & last column)
    let ws_data = [headers];
    data.forEach(function (row) {
        let cleanRow = row.slice(1, -1).map((val, i) => {
            // Assuming Amount Pawned is the last numeric column before "Contact"
            if (i === row.slice(1, -1).length - 2) {
                return formatMoney(val); // ensure numeric .00
            }
            return val;
        });
        ws_data.push(cleanRow);
    });

    // Add footer total aligned with "Amount Pawned" column
    let totalText = document.getElementById("pawned_total_amount").innerText;
    let totalVal = formatMoney(totalText);
    let footerRow = new Array(headers.length).fill("");
    footerRow[3] = "Total";      // label in "Category" column
    footerRow[4] = totalVal;     // numeric total under "Amount Pawned"
    ws_data.push(footerRow);


    // Create worksheet & workbook
    let ws = XLSX.utils.aoa_to_sheet(ws_data);

    // Apply borders to all cells
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let R = range.s.r; R <= range.e.r; ++R) {
        for (let C = range.s.c; C <= range.e.c; ++C) {
            let cell_ref = XLSX.utils.encode_cell({ c: C, r: R });
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

    // Workbook setup
    let wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Pawned Items");

    // Filename with date filter
    let fromDate = document.getElementById("pawned_fromDate").value || "ALL";
    let toDate = document.getElementById("pawned_toDate").value || "ALL";
    let filename = `pawn_report_${fromDate}_${toDate}.xlsx`;

    // Export file
    XLSX.writeFile(wb, filename);
});

