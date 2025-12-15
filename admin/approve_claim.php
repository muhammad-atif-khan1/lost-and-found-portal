<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Check claim ID
if (!isset($_GET['id'])) {
    die("Invalid claim ID.");
}

$claim_id = (int)$_GET['id'];

// 1️⃣ Get claim details
$stmt = $pdo->prepare("SELECT * FROM claims WHERE claim_id = ?");
$stmt->execute([$claim_id]);
$claim = $stmt->fetch();

if (!$claim) {
    die("Claim not found.");
}

$item_id = $claim['item_id'];

// 2️⃣ Approve claim
$pdo->prepare("UPDATE claims SET status = 'approved' WHERE claim_id = ?")
    ->execute([$claim_id]);

// 3️⃣ Mark item as returned
$pdo->prepare("UPDATE items SET status = 'returned' WHERE item_id = ?")
    ->execute([$item_id]);

// Redirect back
header("Location: manage_claims.php?success=1");
exit;
