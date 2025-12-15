<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Item ID required
if (!isset($_GET['id'])) {
    die("Item not found.");
}

$item_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch();

if (!$item || $item['type'] !== 'found') {
    die("Invalid item.");
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Image upload
    $proof_image = null;
    if (!empty($_FILES['proof']['name'])) {
        $fileName = time() . "_" . basename($_FILES['proof']['name']);
        $target = __DIR__ . '/../uploads/' . $fileName;


        if (move_uploaded_file($_FILES['proof']['tmp_name'], $target)) {
            $proof_image = $fileName;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO claims (item_id, claimant_name, phone, message, proof_image) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$item_id, $name, $phone, $message, $proof_image]);

    echo "<script>alert('Claim submitted! Admin will review it.');window.location='index.php';</script>";
    exit;
}

include __DIR__ . '/../views/partials/header.php';
?>

<div class="container mt-4">
    <h3>Claim Item: <?= htmlspecialchars($item['title']) ?></h3>
    
    <form method="POST" enctype="multipart/form-data" class="mt-3">

        <label>Your Name</label>
        <input type="text" name="name" class="form-control" required>

        <label class="mt-2">Phone</label>
        <input type="text" name="phone" class="form-control" required>

        <label class="mt-2">Why do you believe this item is yours?</label>
        <textarea name="message" class="form-control" required></textarea>

        <label class="mt-2">Upload Proof (optional)</label>
        <input type="file" name="proof" class="form-control">

        <button class="btn btn-primary mt-3">Submit Claim</button>
    </form>
</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
