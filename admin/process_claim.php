<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die("Invalid request.");
}

$claim_id = (int)$_GET['id'];
$action   = $_GET['action'];

// Fetch claim info first
$stmt = $pdo->prepare("SELECT * FROM claims WHERE claim_id = ?");
$stmt->execute([$claim_id]);
$claim = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$claim) {
    die("Claim not found.");
}

$item_id = $claim['item_id'];

if ($action === "approve") {

    // 1. Approve selected claim
    $approve = $pdo->prepare("UPDATE claims SET status='approved' WHERE claim_id=?");
    $approve->execute([$claim_id]);

    // 2. Reject all other claims for same item
    $rejectOthers = $pdo->prepare("UPDATE claims SET status='rejected' WHERE item_id=? AND claim_id != ?");
    $rejectOthers->execute([$item_id, $claim_id]);

    // 3. Update item status to RETURNED
    $updateItem = $pdo->prepare("UPDATE items SET status='returned' WHERE item_id=?");
    $updateItem->execute([$item_id]);

    $_SESSION['message'] = "Claim approved and item marked as returned.";

} elseif ($action === "reject") {

    // Reject only this claim
    $reject = $pdo->prepare("UPDATE claims SET status='rejected' WHERE claim_id=?");
    $reject->execute([$claim_id]);

    $_SESSION['message'] = "Claim rejected.";

} else {
    die("Invalid action.");
}

header("Location: manage_claims.php");
exit;
