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
            { data: null }, // Auto-number column
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
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();
            let intVal = i => typeof i === "string"
                ? i.replace(/[₱,]/g, "")*1
                : typeof i === "number"
                    ? i
                    : 0;

            let total = api
                .column(6, { page: "current" })
                .data()
                .reduce((a, b) => intVal(a) + intVal(b), 0);

            $(api.column(6).footer()).html(
                "₱" + total.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );
        }
    });

    // Auto-number rows
    tuboTable.on("order.dt search.dt draw.dt", function () {
        tuboTable.column(0, { search: "applied", order: "applied" })
            .nodes()
            .each((cell, i) => {
                cell.innerHTML = i + 1;
            });
    });

    // Filters
    $("#tubo_filterBtn").on("click", function() { tuboTable.ajax.reload(); });
    $("#tubo_resetBtn").on("click", function() {
        $("#tubo_branchFilter").val('');
        $("#tubo_fromDate").val('');
        $("#tubo_toDate").val('');
        tuboTable.ajax.reload();
    });
    $("#tubo_branchFilter, #tubo_fromDate, #tubo_toDate").on("change", function(){
        tuboTable.ajax.reload();
    });

    // -----------------
    // EXPORT FUNCTIONS
    // -----------------

    // Print
    $("#tubo_print").on("click", function () {

         if (userRole === 'super_admin') {
        // Super admin → use dropdown or default to "All Branches"
            branch = $('#tubo_branchFilter option:selected').text() || 'All Branches';
        } else {
            // Admin/cashier → always their assigned branch
            branch = userBranch || 'My Branch';
        }


        // let branch = $("#tubo_branchFilter option:selected").text() || "All Branches";
        let fromDate = $("#tubo_fromDate").val() || "N/A";
        let toDate = $("#tubo_toDate").val() || "N/A";
        let tableHtml = document.getElementById("tuboTable").outerHTML;

        let printWindow = window.open("", "", "width=900,height=700");
        printWindow.document.write(`
            <html><head>
                <title>Tubo Payments Report</title>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    h2, h4 { text-align: center; margin: 0; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                    tfoot th { font-weight: bold; text-align: center; border: none }
                    .filters { margin-top: 10px; font-size: 12px; text-align: center; }
                </style>
            </head><body>
                <h2>Tubo Payments Report</h2>
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
    $("#tubo_export_pdf").on("click", function () {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF("l", "mm", "a4");

            if (userRole === 'super_admin') {
            // Super admin → use dropdown or default to "All Branches"
            branch = $('#tubo_branchFilter option:selected').text() || 'All Branches';
        } else {
            // Admin/cashier → always their assigned branch
            branch = userBranch || 'My Branch';
        }

        // let branch = $("#tubo_branchFilter option:selected").text() || "All Branches";
        let fromDate = $("#tubo_fromDate").val() || "N/A";
        let toDate = $("#tubo_toDate").val() || "N/A";

        doc.setFontSize(14);
        doc.text("Tubo Payments Report", doc.internal.pageSize.getWidth() / 2, 15, { align: "center" });
        doc.setFontSize(10);
        doc.text(`Branch: ${branch} | From: ${fromDate} | To: ${toDate}`, doc.internal.pageSize.getWidth() / 2, 22, { align: "center" });

        let headers = [];
        $("#tuboTable thead th").each(function () { headers.push($(this).text().trim()); });

        let data = [];
        $("#tuboTable tbody tr").each(function () {
            let row = [];
            $(this).find("td").each(function () {
                let text = $(this).text().trim();
                text = text.replace(/₱/g, "PHP "); // replace peso sign
                row.push(text);
            });
            data.push(row);
        });

        let totalInterest = document.querySelector("#tubo_total_interest").innerText;
        totalInterest = totalInterest.replace(/₱/g, "PHP ");

        doc.autoTable({
            head: [headers],
            body: data,
            foot: [[{ content: "Total Interest", colSpan: 6, styles: { halign: "right" } }, { content: totalInterest }]],
            startY: 30,
            styles: { fontSize: 8 }
        });

        doc.save("tubo-payments-report.pdf");
    });

    // Export Excel
    $("#tubo_export_excel").on("click", function () {
        let table = $("#tuboTable").DataTable();
        let data = table.rows({ search: "applied" }).data().toArray();
        let headers = [];
        $("#tuboTable thead th").each(function () { headers.push($(this).text().trim()); });

        function formatMoney(val) {
            if (!val) return 0.00;
            let num = parseFloat(String(val).replace(/[^0-9.-]+/g, "")) || 0;
            return parseFloat(num.toFixed(2));
        }

        let ws_data = [headers];
        data.forEach((row, i) => {
            ws_data.push([
                i + 1, // auto numbering
                row.item,
                row.owner,
                row.date_paid,
                `${row.period_start} to ${row.period_end}`,
                row.months_covered,
                formatMoney(row.interest_amount)
            ]);
        });

        let total = formatMoney(document.getElementById("tubo_total_interest").innerText);
        ws_data.push(["", "", "", "", "", "Total Interest", total]);

        let ws = XLSX.utils.aoa_to_sheet(ws_data);
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Tubo Payments");
        let filename = `tubo_payments_${$("#tubo_fromDate").val() || "ALL"}_${$("#tubo_toDate").val() || "ALL"}.xlsx`;
        XLSX.writeFile(wb, filename);
    });
});
