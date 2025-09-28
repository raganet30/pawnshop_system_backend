<?php
session_start();
require_once "../config/db.php";
require_once "../config/helpers.php";


header('Content-Type: application/json');

try {
    // --- Authentication ---
    if (!isset($_SESSION['user'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized"]);
        exit;
    }

    $pawn_id = $_POST['pawn_id'] ?? null;
    $branch_id = $_SESSION['user']['branch_id'];
    $user_id = $_SESSION['user']['id'];
    $notes = $_POST['claimNotes'] ?? "";
    $date_claimed = $_POST['claimDate'] ?? "";

    // --- From frontend ---
    $months = isset($_POST['claimMonthsValue']) ? trim($_POST['claimMonthsValue']) : "";
    $interest_amount = isset($_POST['claimInterestValue']) && $_POST['claimInterestValue'] !== ''
        ? floatval($_POST['claimInterestValue'])
        : 0.00;
    $total_paid = isset($_POST['claimTotalValue']) && $_POST['claimTotalValue'] !== ''
        ? floatval($_POST['claimTotalValue'])
        : 0.00;
    $penalty = isset($_POST['claimPenalty']) && $_POST['claimPenalty'] !== ''
        ? floatval($_POST['claimPenalty'])
        : 0.00;




    if (!$pawn_id) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID."]);
        exit;
    }

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    $pawn_item = htmlspecialchars($pawn['unit_description']);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawn record not found or already claimed."]);
        exit;
    }


    $interest_rate = floatval($pawn['interest_rate'] ?? 0.06); // decimal format
    $principal = floatval($pawn['amount_pawned']);



    // --- Save claimant photo ---
    $photoPathForDb = null;
    if (!empty($_POST['claimantPhoto'])) {
        $photoData = $_POST['claimantPhoto'];
        $photoData = str_replace('data:image/png;base64,', '', $photoData);
        $photoData = str_replace(' ', '+', $photoData);
        $photoBinary = base64_decode($photoData);

        $fileName = "claimant_" . $pawn_id . "_" . time() . ".png";
        $uploadDir = "../uploads/claimants/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . $fileName;
        file_put_contents($filePath, $photoBinary);
        $photoPathForDb = "uploads/claimants/" . $fileName;
    } else {
        echo json_encode(["status" => "error", "message" => "Claimant photo is required."]);
        exit;
    }


    // Sanitize input
    $penalty = isset($_POST['claimPenalty']) && $_POST['claimPenalty'] !== '' ? floatval($_POST['claimPenalty']) : 0.00;
    // $total_paid = $principal + $interest_amount + $penalty;


    // --- Insert into claims ---
   $stmt = $pdo->prepare("
    INSERT INTO claims 
    (pawn_id, branch_id, date_claimed, months, interest_rate, interest_amount, principal_amount, penalty_amount, total_paid, cashier_id, notes, photo_path, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
$stmt->execute([
    $pawn_id,               // pawn_id
    $pawn['branch_id'],     // branch_id
    $date_claimed,
    $months,                // months (string from frontend)
    $interest_rate,         // interest_rate
    $interest_amount,       // interest_amount
    $principal,             // principal_amount
    $penalty,               // penalty_amount
    $total_paid,            // total_paid
    $user_id,               // cashier_id
    $notes,                 // notes
    $photoPathForDb         // photo_path
]);


    $claim_id = $pdo->lastInsertId();

    // --- Update pawned item ---
    $stmt = $pdo->prepare("UPDATE pawned_items SET status = 'claimed' WHERE pawn_id = ?");
    $stmt->execute([$pawn_id]);

    // --- Update branch cash on hand ---
    updateCOH($pdo, $branch_id, $total_paid, 'add');



    // --- Insert into cash ledger ---
    // Calculate total amount for ledger (penalty + interest)
    // $total_amount = $penalty + $interest_amount ;



    // Ledger entry (Cash IN for total claim amount)
    if ($total_paid > 0) {
        $description = "Claim (ID #$claim_id)";
        $notes = "Pawn ID #$pawn_id claimed with interest + penalty (if any)";

        insertCashLedger(
            $pdo,
            $branch_id,
            "claim",     // txn_type
            "in",        // direction
            $total_paid,
            "claims",    // ref_table
            $pawn_id,
            $description,
            $notes,
            $user_id
        );
    }


// Fetch interest option from form (modal)
$interestOption = $_POST['interestOption'] ?? 'auto'; 
$custom_interest = isset($_POST['customInterest']) ? floatval($_POST['customInterest']) : null;


// --- Decide action type and description based on interestOption ---
//$actionType = 'Claimed Pawned Item (auto compute)   '; //  default
$interestNote = '';

if (isset($interestOption)) {
    if ($interestOption === 'waive') {   //  match the HTML value
        $actionType = 'Claimed Pawned Item (interest waived)';
        $interestNote = ' | Interest: Waived';
    } elseif ($interestOption === 'custom') {
        $actionType = 'Claimed Pawned Item (custom interest)';
        $interestNote = sprintf(' | Custom Interest: ₱%s', number_format($custom_interest ?? 0, 2));
    }
    elseif ($interestOption === 'auto') {
        $actionType = 'Claimed Pawned Item (auto compute)';
        $interestNote = sprintf(' | Interest: ₱%s', number_format($interest_amount ?? 0, 2));
    } else {
        $actionType = 'Claimed Pawned Item (auto compute)'; // fallback
        $interestNote = sprintf(' | Interest: ₱%s', number_format($interest_amount ?? 0, 2));
    }
}


// --- Insert into audit_logs ---
$description = sprintf(
    "Claimed pawn item: %s, Category: %s, Total Amount Paid: ₱%s%s",
    $pawn_item,
    $pawn['category'],
    number_format($total_paid, 2),
    $interestNote
);

logAudit(
    $pdo,
    $user_id,
    $pawn['branch_id'],
    $actionType,
    $description
);





    //if has records on partial payments update related records to settled
    //update partial_payments status to settled
    // --- Update partial payments set status to settled ---
    $stmt = $pdo->prepare("UPDATE partial_payments SET status = 'settled' WHERE pawn_id = ? AND status = 'active'");
    $stmt->execute([$pawn_id]);

    // update latest partial transaction set to 0 remaining balance
    $stmt = $pdo->prepare(" UPDATE partial_payments SET remaining_principal = 0 WHERE pawn_id = ? ORDER BY pp_id DESC LIMIT 1");
    $stmt->execute([$pawn_id]);





    echo json_encode([
        "status" => "success",
        "message" => "Pawn item successfully claimed!<br>Cash on Hand +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
