// function printClaimReceipt(d) {
//     // Calculate months period
//     const start = new Date(d.date_pawned);
//     const end = new Date(d.date_claimed);

//     // Difference in milliseconds
//     const diffTime = end - start;

//     // Convert to days
//     const diffDays = diffTime / (1000 * 60 * 60 * 24);

//     // Calculate full months
//     let months = (end.getFullYear() - start.getFullYear()) * 12 +
//         (end.getMonth() - start.getMonth());

//     // If there are extra days beyond full months, add 1 month
//     if (end.getDate() - start.getDate() > 0) {
//         months += 1;
//     }

//     // Ensure minimum 1 month
//     if (months <= 0) {
//         months = 1;
//     }

//     d.months_duration = months;


//     let receipt = "";
//     receipt += centerText("LD Pawnshop", 80) + "\n";
//     receipt += centerText(d.branch_name, 80) + "\n";
//     receipt += centerText(d.branch_address, 80) + "\n";
//     receipt += centerText("Cell: " + d.branch_phone, 80) + "\n";
//     receipt += "-".repeat(80) + "\n";
//     receipt += pad("OR No.      : " + d.or_no, 80) + "\n";
//     receipt += pad("Customer    : " + d.full_name, 80) + "\n";
//     receipt += pad("Contact No. : " + d.contact_no, 80) + "\n";
//     receipt += pad("Address     : " + d.address, 80) + "\n";
//     receipt += "-".repeat(80) + "\n";
//     receipt += pad("Pawned Item : " + d.unit_description, 80) + "\n";
//     receipt += pad("Category    : " + d.category, 80) + "\n";
//     receipt += pad("Date Pawned : " + d.date_pawned, 80) + "\n";
//     receipt += pad("Date Claimed: " + d.date_claimed, 80) + "\n";
//     receipt += pad("Period      : " + d.months_duration + " month(s)", 80) + "\n";
//     receipt += "-".repeat(80) + "\n";
//     receipt += pad("Description", 40) + pad("Amount", 40, "right") + "\n";
//     receipt += "-".repeat(80) + "\n";
//     receipt += pad("Amount Pawned", 40) + pad(formatMoney(d.amount_pawned), 40, "right") + "\n";
//     receipt += pad("Interest", 40) + pad(formatMoney(d.interest_amount), 40, "right") + "\n";
//     receipt += pad("Penalty", 40) + pad(formatMoney(d.penalty_amount), 40, "right") + "\n";
//     receipt += "-".repeat(80) + "\n";
//     receipt += pad("TOTAL     : ", 40) + pad("₱" + formatMoney(d.total_paid), 40, "right") + "\n";
//     receipt += "-".repeat(80) + "\n\n";
//     receipt += pad("Cashier   : " + d.cashier, 80) + "\n";
//     receipt += pad("Printed   : " + d.printed_at, 80) + "\n\n";
//     receipt += centerText("Thank you!", 80) + "\n\n\n";

//     // Open print window
//     let w = window.open("", "PrintWindow", "width=800,height=600");
//     w.document.write("<pre>" + receipt + "</pre>");
//     w.document.close();
//     w.print();
// }

// // Helper functions
// function pad(text, width, align = "left") {
//     text = text.toString();
//     if (text.length >= width) return text.slice(0, width);
//     if (align === "right") return " ".repeat(width - text.length) + text;
//     return text + " ".repeat(width - text.length);
// }

// function centerText(text, width) {
//     text = text.toString();
//     if (text.length >= width) return text.slice(0, width);
//     const left = Math.floor((width - text.length) / 2);
//     const right = width - text.length - left;
//     return " ".repeat(left) + text + " ".repeat(right);
// }

// function formatMoney(num) {
//     return parseFloat(num).toLocaleString("en-PH", {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2
//     });
// }



// function printClaimReceipt(d) {
//     // Calculate months period
//     const start = new Date(d.date_pawned);
//     const end = new Date(d.date_claimed);
//     let months = (end.getFullYear() - start.getFullYear()) * 12 + (end.getMonth() - start.getMonth());
//     if (end.getDate() - start.getDate() > 0) months += 1;
//     if (months <= 0) months = 1;
//     d.months_duration = months;

//     let receipt = "";
//     const lineWidth = 42;

//     receipt += centerText("LD Pawnshop", lineWidth) + "\n";
//     receipt += centerText(d.branch_name, lineWidth) + "\n";
//     receipt += centerText(d.branch_address, lineWidth) + "\n";
//     receipt += centerText("Cell: " + d.branch_phone, lineWidth) + "\n";
//     receipt += "-".repeat(lineWidth) + "\n";

//     receipt += pad("OR No.      : " + d.or_no, lineWidth) + "\n";
//     receipt += pad("Customer    : " + d.full_name, lineWidth) + "\n";
//     receipt += pad("Contact No. : " + d.contact_no, lineWidth) + "\n";
//     receipt += pad("Address     : " + d.address, lineWidth) + "\n";
//     receipt += "-".repeat(lineWidth) + "\n";

//     receipt += pad("Pawned Item : " + d.unit_description, lineWidth) + "\n";
//     receipt += pad("Category    : " + d.category, lineWidth) + "\n";
//     receipt += pad("Date Pawned : " + d.date_pawned, lineWidth) + "\n";
//     receipt += pad("Date Claimed: " + d.date_claimed, lineWidth) + "\n";
//     receipt += pad("Period      : " + d.months_duration + " month(s)", lineWidth) + "\n";
//     receipt += "-".repeat(lineWidth) + "\n";

//     // Columns for Description & Amount
//     const descWidth = 22;
//     const amountWidth = 20;

//     receipt += pad("Description", descWidth) + pad("Amount", amountWidth, "right") + "\n";
//     receipt += "-".repeat(lineWidth) + "\n";

//     receipt += pad("Amount Pawned", descWidth) + pad(formatMoney(d.amount_pawned), amountWidth, "right") + "\n";
//     receipt += pad("Interest", descWidth) + pad(formatMoney(d.interest_amount), amountWidth, "right") + "\n";
//     receipt += pad("Penalty", descWidth) + pad(formatMoney(d.penalty_amount), amountWidth, "right") + "\n";
//     receipt += "-".repeat(lineWidth) + "\n";

//     receipt += pad("TOTAL     : ", descWidth) + pad("₱" + formatMoney(d.total_paid), amountWidth, "right") + "\n";
//     receipt += "-".repeat(lineWidth) + "\n\n";

//     receipt += pad("Cashier   : " + d.cashier, lineWidth) + "\n";
//     receipt += pad("Printed   : " + d.printed_at, lineWidth) + "\n\n";

//     receipt += centerText("Thank you!", lineWidth) + "\n\n\n";

//     // Open print window
//     let w = window.open("", "PrintWindow", "width=800,height=600");
//     w.document.write("<pre>" + receipt + "</pre>");
//     w.document.close();
//     w.print();
// }

// // Helper functions
// function pad(text, width, align = "left") {
//     text = text.toString();
//     if (text.length >= width) return text.slice(0, width);
//     if (align === "right") return " ".repeat(width - text.length) + text;
//     return text + " ".repeat(width - text.length);
// }

// function centerText(text, width) {
//     text = text.toString();
//     if (text.length >= width) return text.slice(0, width);
//     const left = Math.floor((width - text.length) / 2);
//     const right = width - text.length - left;
//     return " ".repeat(left) + text + " ".repeat(right);
// }

// function formatMoney(num) {
//     return parseFloat(num).toLocaleString("en-PH", {
//         minimumFractionDigits: 2,
//         maximumFractionDigits: 2
//     });
// }