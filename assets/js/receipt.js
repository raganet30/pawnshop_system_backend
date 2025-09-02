function printClaimReceipt(d) {
    // Calculate months duration
    const start = new Date(d.date_pawned);
    const end = new Date(d.date_claimed);
    let months = (end.getFullYear() - start.getFullYear()) * 12 +
        (end.getMonth() - start.getMonth());
    if (end.getDate() - start.getDate() > 0) months += 1;
    if (months <= 0) months = 1;
    d.months_duration = months;

    // Helper for compact date format
    function formatDate(date) {
        const dt = new Date(date);
        const day = String(dt.getDate()).padStart(2, '0');
        const month = String(dt.getMonth() + 1).padStart(2, '0');
        const year = dt.getFullYear();
        return `${day}/${month}/${year}`;
    }


    // Receipt construction
    const lineWidth = 80;
    const descWidth = 35;
    const amountWidth = 45;

    let receipt = "";
    receipt += centerText("LD Gadget Pawnshop", lineWidth) + "\n";
    receipt += centerText(d.branch_name, lineWidth) + "\n";
    receipt += centerText(d.branch_address, lineWidth) + "\n";
    receipt += centerText("Cell: " + d.branch_phone, lineWidth) + "\n";
    receipt += "-".repeat(lineWidth) + "\n";



    // Transform date_claimed into MMDDYY
    function formatDateTwist(dateStr) {
        const [year, month, day] = dateStr.split("-"); // ["2025","09","02"]
        return month + day + year.slice(2);            // "090225"
    }

    // Example usage
    let twistedDate = formatDateTwist(d.date_claimed);
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

    // Columns for Description & Amount
    receipt += pad("Description", descWidth) + pad("Amount", amountWidth, "right") + "\n";
    receipt += "-".repeat(lineWidth) + "\n";

    receipt += pad("Amount Pawned", descWidth) + pad("₱" + formatMoney(d.amount_pawned), amountWidth, "right") + "\n";
    receipt += pad("Interest", descWidth) + pad("₱" + formatMoney(d.interest_amount), amountWidth, "right") + "\n";
    receipt += pad("Penalty", descWidth) + pad("₱" + formatMoney(d.penalty_amount), amountWidth, "right") + "\n";
    receipt += "-".repeat(lineWidth) + "\n";

    receipt += pad("TOTAL     : ", descWidth) + pad("₱" + formatMoney(d.total_paid), amountWidth, "right") + "\n";
    receipt += "-".repeat(lineWidth) + "\n\n";

    receipt += pad("Cashier   : " + d.cashier, lineWidth) + "\n";
    receipt += pad("Printed   : " + formatDate(d.printed_at), lineWidth) + "\n\n";

    receipt += centerText("Thank you!", lineWidth) + "\n\n\n";

    // Open print window
    let w = window.open("", "PrintWindow", "width=800,height=600");
    w.document.write("<pre style='font-family: Courier; font-size: 12px;'>" + receipt + "</pre>");
    w.document.close();
    w.print();
}

// Helper functions
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
    return parseFloat(num).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
