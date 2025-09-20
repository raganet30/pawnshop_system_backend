$(document).ready(function () {
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
            { data: null }, // auto-number
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

            let totalPayment = api.column(4, {page:'current'}).data()
                .reduce((a,b)=>intVal(a)+intVal(b),0);

            $(api.column(4).footer()).html(
                '₱'+totalPayment.toLocaleString(undefined,{minimumFractionDigits:2})
            );
        }
    });

    // Auto numbering
    partialTable.on('order.dt search.dt draw.dt', function () {
        partialTable.column(0, { search: "applied", order: "applied" })
            .nodes()
            .each((cell, i) => {
                cell.innerHTML = i + 1;
            });
    });

    // Filters
    $('#partial_filterBtn').on('click', ()=> partialTable.ajax.reload());
    $('#partial_resetBtn').on('click', ()=>{
        $('#partial_branchFilter').val('');
        $('#partial_fromDate').val('');
        $('#partial_toDate').val('');
        partialTable.ajax.reload();
    });

    // -----------------
    // EXPORT FUNCTIONS
    // -----------------

    // Print
    $("#partial_print").on("click", function () {

         if (userRole === 'super_admin') {
        // Super admin → use dropdown or default to "All Branches"
            branch = $('#partial_branchFilter option:selected').text() || 'All Branches';
        } else {
            // Admin/cashier → always their assigned branch
            branch = userBranch || 'My Branch';
        }


        // let branch = $("#partial_branchFilter option:selected").text() || "All Branches";
        let fromDate = $("#partial_fromDate").val() || "N/A";
        let toDate = $("#partial_toDate").val() || "N/A";
        let tableHtml = document.getElementById("partialPaymentsTable").outerHTML;

        let printWindow = window.open("", "", "width=900,height=700");
        printWindow.document.write(`
            <html><head>
                <title>Partial Payments Report</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    h2, h4 { text-align: center; margin: 0; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                    tfoot th { font-weight: bold; text-align: center; border: none }
                    .filters { margin-top: 10px; font-size: 12px; text-align: center; }
                </style>
            </head><body>
                <h2>Partial Payments Report</h2>
                <div class="filters">Branch: ${branch} | From: ${fromDate} | To: ${toDate}</div>
                ${tableHtml}
            </body></html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });

    // Export PDF
    $("#partial_export_pdf").on("click", function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");

         if (userRole === 'super_admin') {
        // Super admin → use dropdown or default to "All Branches"
            branch = $('#partial_branchFilter option:selected').text() || 'All Branches';
        } else {
            // Admin/cashier → always their assigned branch
            branch = userBranch || 'My Branch';
        }


       // let branch = $("#partial_branchFilter option:selected").text() || "All Branches";
        let fromDate = $("#partial_fromDate").val() || "N/A";
        let toDate = $("#partial_toDate").val() || "N/A";

        doc.setFontSize(14);
        doc.text("Partial Payments Report", doc.internal.pageSize.getWidth() / 2, 15, { align: "center" });
        doc.setFontSize(10);
        doc.text(`Branch: ${branch} | From: ${fromDate} | To: ${toDate}`, doc.internal.pageSize.getWidth() / 2, 22, { align: "center" });

        let headers = [];
        $("#partialPaymentsTable thead th").each(function () { headers.push($(this).text().trim()); });

        let data = [];
        $("#partialPaymentsTable tbody tr").each(function () {
            let row = [];
            $(this).find("td").each(function () {
                let text = $(this).text().trim();
                text = text.replace(/₱/g, "PHP ");
                row.push(text);
            });
            data.push(row);
        });

        let totalPayment = $("#partialPaymentsTable tfoot th:eq(1)").text();
        totalPayment = totalPayment.replace(/₱/g, "PHP ");

        doc.autoTable({
            head: [headers],
            body: data,
            foot: [[{ content: "Total Payment", colSpan: 4, styles: { halign: "right" } }, { content: totalPayment }, { content: "" }, { content: "" }]],
            startY: 30,
            styles: { fontSize: 8 }
        });

        doc.save("partial-payments-report.pdf");
    });

    // Export Excel
    $("#partial_export_excel").on("click", function () {
        let table = $("#partialPaymentsTable").DataTable();
        let data = table.rows({ search: "applied" }).data().toArray();
        let headers = [];
        $("#partialPaymentsTable thead th").each(function () { headers.push($(this).text().trim()); });

        function formatMoney(val) {
            if (!val) return 0.00;
            let num = parseFloat(String(val).replace(/[^0-9.-]+/g, "")) || 0;
            return parseFloat(num.toFixed(2));
        }

        let ws_data = [headers];
        data.forEach((row, i) => {
            ws_data.push([
                i + 1,
                row.date_paid,
                row.customer,
                row.item,
                formatMoney(row.amount_paid),
                formatMoney(row.remaining_balance),
                row.status
            ]);
        });

        let total = $("#partialPaymentsTable tfoot th:eq(1)").text();
        total = parseFloat(total.replace(/[^0-9.-]+/g, "")) || 0;
        ws_data.push(["", "", "", "Total Payment", total, "", ""]);

        let ws = XLSX.utils.aoa_to_sheet(ws_data);
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Partial Payments");
        let filename = `partial_payments_${$("#partial_fromDate").val() || "ALL"}_${$("#partial_toDate").val() || "ALL"}.xlsx`;
        XLSX.writeFile(wb, filename);
    });
});
