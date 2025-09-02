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
    $notes = $_POST['notes'] ?? "";

    if (!$pawn_id) {
        echo json_encode(["status" => "error", "message" => "Invalid pawn ID."]);
        exit;
    }

    // --- Fetch pawned item ---
    $stmt = $pdo->prepare("SELECT * FROM pawned_items WHERE pawn_id = ? AND status = 'pawned'");
    $stmt->execute([$pawn_id]);
    $pawn = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pawn) {
        echo json_encode(["status" => "error", "message" => "Pawn record not found or already claimed."]);
        exit;
    }

    // --- Calculate months pawned ---
    $datePawned = new DateTime($pawn['date_pawned']);
    $now = new DateTime();
    $months = max(1, ceil($datePawned->diff($now)->days / 30));

    // --- Get branch interest rate ---
    $stmt = $pdo->prepare("SELECT interest_rate, cash_on_hand FROM branches WHERE branch_id = ?");
    $stmt->execute([$pawn['branch_id']]); // use pawn's branch
    $branch = $stmt->fetch(PDO::FETCH_ASSOC);

    $interest_rate = floatval($branch['interest_rate'] ?? 0.06); // decimal format
    $principal = floatval($pawn['amount_pawned']);
    $interest_amount = round($principal * $interest_rate * $months, 2);
    $total_paid = round($principal + $interest_amount, 2);

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
    $total_paid = $principal + $interest_amount + $penalty;

    // --- Insert into claims ---
    $stmt = $pdo->prepare("
    INSERT INTO claims 
    (pawn_id, branch_id, date_claimed, months, interest_rate, interest_amount, principal_amount, penalty_amount, total_paid, cashier_id, notes, photo_path, created_at)
    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
");
    $stmt->execute([
        $pawn_id,               // pawn_id
        $pawn['branch_id'],     // branch_id
        $months,                // months
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



    // ✅ Ledger entry (Cash IN for total claim amount)
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



    // --- Insert into audit_logs ---
    $description = sprintf(
        "Claimed pawn ID: %d, Unit: %s, Total Amount Paid: ₱%s",
        $pawn_id,
        $pawn['unit_description'],
        number_format($total_paid, 2)
    );

    logAudit(
        $pdo,
        $user_id,
        $pawn['branch_id'],
        'Claim Pawned Item',
        $description
    );


    // --- Insert into tubo_payments ---
    $stmt = $pdo->prepare("
    INSERT INTO tubo_payments 
    (pawn_id, branch_id, date_paid, months_covered, interest_rate, interest_amount, cashier_id, notes, created_at)
    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, NOW())
");
    $stmt->execute([
        $pawn_id,             // pawn_id
        $pawn['branch_id'],   // branch_id
        $months,              // months_covered
        $interest_rate,       // interest_rate (decimal, e.g., 0.06)
        $interest_amount,     // interest_amount
        $user_id,             // cashier_id
        "Claim payment"       // notes
    ]);




    //update partial_payments status to settled
    // --- Update partial payments ---
    $stmt = $pdo->prepare("UPDATE partial_payments SET status = 'settled', remaining_principal = 0  WHERE pawn_id = ? AND status = 'active'");
    $stmt->execute([$pawn_id]);





    echo json_encode([
        "status" => "success",
        "message" => "Pawn item successfully claimed!<br>Cash on Hand +₱" . number_format($total_paid, 2),
        "pawn_id" => $pawn_id
    ]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
