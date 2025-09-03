function printClaimReceipt(d) {
    // Compute months duration
    const start = new Date(d.date_pawned);
    const end = new Date(d.date_claimed);
    let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
    if (end.getDate() - start.getDate() > 0) months += 1;
    if (months <= 0) months = 1;
    d.months_duration = months;

    // Helpers
    function formatDate(date) {
        const dt = new Date(date);
        const day = String(dt.getDate()).padStart(2, '0');
        const month = String(dt.getMonth() + 1).padStart(2, '0');
        const year = dt.getFullYear();
        return `${day}/${month}/${year}`;
    }
    function formatDateTwist(dateStr) {
        const [year, month, day] = dateStr.split("-");
        return month + day + year.slice(2); // "MMDDYY"
    }

    const lineWidth = 80;
    let receipt = "";

    // Header
    receipt += centerText("LD Gadget Pawnshop", lineWidth) + "\n";
    receipt += centerText(d.branch_name, lineWidth) + "\n";
    receipt += centerText(d.branch_address, lineWidth) + "\n";
    receipt += centerText("Cell: " + d.branch_phone, lineWidth) + "\n";
    receipt += "-".repeat(lineWidth) + "\n";

    const twistedDate = formatDateTwist(d.date_claimed);
    receipt += pad("OR No.      : " + d.pawn_id + twistedDate, lineWidth) + "\n";
    receipt += pad("Customer    : " + d.full_name, lineWidth) + "\n";
    receipt += pad("Contact No. : " + d.contact_no, lineWidth) + "\n";
    receipt += pad("Address     : " + d.address, lineWidth) + "\n";
    receipt += "-".repeat(lineWidth) + "\n";
    receipt += pad("Pawned Item : " + d.unit_description, lineWidth) + "\n";
    receipt += pad("Category    : " + d.category, lineWidth) + "\n";
    receipt += pad("Date Pawned : " + formatDate(d.date_pawned), lineWidth) + "\n";
    receipt += pad("Date Claimed: " + formatDate(d.date_claimed), lineWidth) + "\n";
    receipt += pad("Period      : " + d.months_duration + " month(s)", lineWidth) + "\n";
    receipt += "-".repeat(lineWidth) + "\n";

    // -------- Branching: SOA vs Default ----------
    const hasArray = Array.isArray(d.partial_payments);
    const hasSOA = hasArray && d.partial_payments.length > 1; // >1 means has partial history + final row

    if (hasSOA) {
        const dateW = 12, moneyW = 12, balW = 12;

        receipt += centerText("STATEMENT OF ACCOUNT", lineWidth) + "\n";

        // Header row
        let header = pad("Date", dateW) +
            pad("Payment", moneyW, "right") +
            pad("Interest", moneyW, "right") +
            pad("Principal", moneyW, "right") +
            pad("Penalty", moneyW, "right") +
            pad("Balance", balW, "right") + "  REMARKS";
        receipt += header + "\n";
        receipt += "-".repeat(lineWidth) + "\n";

        let totalInterest = 0, totalPenalty = 0, totalPayment = 0;

// ✅ Compute original principal from all rows
let originalPrincipal = d.partial_payments.reduce(
    (sum, p) => sum + parseFloat(p.principal_paid || 0),
    0
);

d.partial_payments.forEach((pp, idx) => {
    const isFirst = idx === 0;
    const isLast = idx === d.partial_payments.length - 1;

    totalPayment += parseFloat(pp.amount_paid || 0);
    totalInterest += parseFloat(pp.interest_paid || 0);

    // Remarks
    let remarks = isLast
        ? "full settlement"
        : (isFirst ? "partial payment"
                   : "partial payment");

    // ✅ Balance recompute dynamically
    let paidPrincipal = d.partial_payments
        .slice(0, idx + 1)
        .reduce((sum, p) => sum + parseFloat(p.principal_paid || 0), 0);

    let displayBalance = originalPrincipal - paidPrincipal;
    if (isLast) displayBalance = 0; // force 0 on settlement row
    if (displayBalance < 0) displayBalance = 0; // safety

    // ✅ Penalty only on last row
    let penaltyPaid = isLast ? parseFloat(d.penalty_amount || 0) : 0;
    if (isLast) totalPenalty += penaltyPaid;

    let row = "";
    row += pad(formatDate(pp.date_paid), dateW);
    row += pad("₱" + formatMoney(pp.amount_paid), moneyW, "right");
    row += pad("₱" + formatMoney(pp.interest_paid), moneyW, "right");
    row += pad("₱" + formatMoney(pp.principal_paid), moneyW, "right");
    row += pad("₱" + formatMoney(penaltyPaid), moneyW, "right");
    row += pad("₱" + formatMoney(displayBalance), balW, "right");
    row += "  " + remarks;

    receipt += row + "\n";
});



        receipt += "-".repeat(lineWidth) + "\n";

        // ✅ Totals section
        receipt += pad("ORIGINAL PRINCIPAL:", 22) + "₱" + formatMoney(originalPrincipal) + "\n";
        receipt += pad("TOTAL INTEREST:", 22) + "₱" + formatMoney(totalInterest) + "\n";
        receipt += pad("TOTAL PENALTY :", 22) + "₱" + formatMoney(totalPenalty) + "\n";
        receipt += pad("TOTAL PAYMENT :", 22) + "₱" + formatMoney(totalPayment + totalPenalty) + "\n";
        receipt += "-".repeat(lineWidth) + "\n\n";
    }

    else {
        // ----- DEFAULT MODE (claimed immediately) -----
        const descWidth = 35;
        const amountWidth = 45;

        receipt += pad("Description", descWidth) + pad("Amount", amountWidth, "right") + "\n";
        receipt += "-".repeat(lineWidth) + "\n";
        receipt += pad("Amount Pawned", descWidth) + pad("₱" + formatMoney(d.amount_pawned), amountWidth, "right") + "\n";
        receipt += pad("Interest", descWidth) + pad("₱" + formatMoney(d.interest_amount), amountWidth, "right") + "\n";
        receipt += pad("Penalty", descWidth) + pad("₱" + formatMoney(d.penalty_amount), amountWidth, "right") + "\n";
        receipt += "-".repeat(lineWidth) + "\n";
        receipt += pad("TOTAL     : ", descWidth) + pad("₱" + formatMoney(d.total_paid), amountWidth, "right") + "\n";
        receipt += "-".repeat(lineWidth) + "\n\n";
    }

    // Footer
    receipt += pad("Cashier   : " + d.cashier, lineWidth) + "\n";
    receipt += pad("Printed   : " + formatDate(d.printed_at), lineWidth) + "\n\n";
    receipt += centerText("Thank you!", lineWidth) + "\n\n\n";

    // Print
    const w = window.open("", "PrintWindow", "width=800,height=600");
    w.document.write("<pre style='font-family: Courier; font-size: 12px;'>" + receipt + "</pre>");


    // ✅ Add QR code after receipt
w.document.write("<div style='text-align:center; margin-top:10px;'>");
w.document.write("<img src='https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=https://www.facebook.com/YourPageHere' alt='QR Code'>");
w.document.write("<div>Scan to visit our Facebook page</div>");
w.document.write("</div>");


    w.document.close();
    w.print();




}

// Helpers
function pad(text, width, align = "left") {
    text = text.toString();
    if (text.length >= width) return text.slice(0, width);
    if (align === "right") return " ".repeat(width - text.length) + text;
    return text + " ".repeat(width - text.length);
}
function centerText(text, width) {
    text = text.toString();
    if (text.length >= width) return text.slice(0, width);
    const left = Math.floor((width - text.length) / 2);
    const right = width - text.length - left;
    return " ".repeat(left) + text + " ".repeat(right);
}
function formatMoney(num) {
    const n = parseFloat(num || 0);
    return n.toLocaleString("en-PH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
