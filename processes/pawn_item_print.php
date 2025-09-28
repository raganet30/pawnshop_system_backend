<?php
require_once "../config/db.php";
session_start();
require_once "../config/helpers.php";


$header = getReceiptHeader($pdo);



// $branch_address = $_SESSION['user']['branch_address'];

$pawn_id = $_GET['id'] ?? null;
if (!$pawn_id) {
    die("Invalid Pawn ID");
}

$stmt = $pdo->prepare("
    SELECT 
        p.pawn_id,
        p.unit_description,
        p.category,
        p.amount_pawned,
        p.interest_rate,
        p.date_pawned,
        p.current_due_date,
        p.notes,
        p.pass_key,
        c.full_name,
        c.contact_no,
        c.address
    FROM pawned_items p
    LEFT JOIN customers c ON p.customer_id = c.customer_id
    WHERE p.pawn_id = ? AND p.is_deleted = 0
");
$stmt->execute([$pawn_id]);
$pawn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pawn) {
    die("Pawn record not found");
}

$interest = $pawn['amount_pawned'] * ($pawn['interest_rate'] );
$totalRepayment = $pawn['amount_pawned'] + $interest;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Pawn Ticket</title>
<style>
    @page {
        size: Letter portrait;  /*  default Letter size */
        margin: 10mm;
    }
    body {
        font-family: "Courier New", monospace;
        font-size: 10pt;
        line-height: 1.1;
        margin: 0;
        padding: 0;
    }
    .receipt {
        width: 100%;
        margin-bottom: 20px;
        page-break-inside: avoid;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td, th {
        padding: 1px 2px;
        vertical-align: top;
    }
    .border td, .border th {
        border: 1px solid black;
        padding: 2px;
    }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .small { font-size: 9pt; }
    .footer-note {
        text-align: right;
        margin-top: 10px;
        font-size: 8pt;
        font-style: italic;
    }
    .cut-line {
        border-top: 1px dashed #000;
        margin: 15px 0;
    }
</style>

</head>
<body onload="window.print()">

<?php 
function renderReceipt($pawn, $header, $totalRepayment, $copyType) { ?>
    <div class="receipt">
        <div class="center bold"><?= htmlspecialchars($header['shop_name']); ?></div>
        <div class="center small"><?= $_SESSION['user']['branch_name']; ?></div>
        <div class="center small"><?= $_SESSION['user']['branch_address']; ?></div>
        <div class="center small">FB Page: <?= htmlspecialchars($header['fb_page_name']); ?></div>
        <br>

        <table class="border small">
            <tr>
                <td><b>PAWNED DATE:</b> <?= date("m/d/Y", strtotime($pawn['date_pawned'])) ?></td>
                <td><b>DUE DATE:</b> <?= date("m/d/Y", strtotime($pawn['current_due_date'])) ?></td>
            </tr>
            <tr>
                <td><b>OWNER:</b> <?= strtoupper($pawn['full_name']) ?></td>
                <td><b>CONTACT #:</b> <?= $pawn['contact_no'] ?></td>
            </tr>
            <tr>
                <td><b>ADDRESS:</b> <?= $pawn['address'] ?></td>
                <td><b>ITEM:</b> <?= $pawn['unit_description'] ?></td>
            </tr>
            <tr>
                <td><b>REMARKS:</b> <?= $pawn['notes'] ?></td>
                <td><b>PASSWORD/PIN:</b> <?= $pawn['pass_key'] ?> </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>AMOUNT:</b> ₱<?= number_format($pawn['amount_pawned'], 2) ?>&nbsp;&nbsp;
                    <b>INTEREST:</b> ₱<?= number_format($pawn['amount_pawned'] * $pawn['interest_rate'], 2) ?>&nbsp;&nbsp;
                    <b>TOTAL REPAYMENT:</b> ₱<?= number_format($totalRepayment, 2) ?>
                </td>
            </tr>
        </table>

        <div class="small">
            <b>Pawner Declarations:</b><br>
            1. Nasa aking pang-unawa na kapag hindi ako nakapagbayad ng kabuuang halaga ng ipinahiram na pera kasama ang interes sa loob ng 2 buwan ay magreresulta ng pagkakaremata ng isinanglang gamit.<br>
            2. Ang battery ng aking gadget ay hindi na responsibilidad ng pawnshop kung sakali mang ito ay masira.<br>
            3. Ang aking pirma sa ibaba ang pagpapatunay na aking naunawaan ang rules and regulations ng pawnshop. <br><br>
            Under the penalty of anti-fencing law, dini-deklara ko po na ako ang may-ari ng item na isinanla.
            <br><br>
        </div>

        <div class="small">
            <b>PAWNER:</b> ______________________ &nbsp;&nbsp;
            <b>CASHIER:</b> ______________________ &nbsp;&nbsp;
            <b>CLAIMED BY:</b> ____________________
        </div>

        <div class="small">
            <b>Business Hours:</b> Mon–Sat (9:00 AM – 6:00 PM)
        </div>

        <div class="footer-note"><?= $copyType ?> Copy</div>
    </div>
<?php } ?>

<?php 
// render two copies
renderReceipt($pawn, $header, $totalRepayment, "Customer's");
echo '<div class="cut-line"></div>'; // cutting line
renderReceipt($pawn, $header, $totalRepayment, "Pawnshop's");
?>

</body>
</html>

