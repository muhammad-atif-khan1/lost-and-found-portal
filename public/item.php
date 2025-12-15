<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';

$itemModel = new ItemModel($pdo);

// Check ID
if (!isset($_GET['id'])) {
    die("Invalid item ID.");
}

$item_id = (int)$_GET['id'];
$item = $itemModel->getById($item_id);

if (!$item) {
    die("Item not found.");
}

// Fetch approved claim if item is returned
$approvedClaim = null;
if ($item['status'] === 'returned') {
    $stmt = $pdo->prepare("SELECT * FROM claims WHERE item_id = ? AND status = 'approved' LIMIT 1");
    $stmt->execute([$item_id]);
    $approvedClaim = $stmt->fetch();
}

// Handle claim submission
$claim_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_submit'])) {

    $claimant_name = $_POST['claimant_name'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $proof_image = null;

    if (!empty($_FILES['proof_image']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/claims/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $proof_image = time() . "_" . basename($_FILES['proof_image']['name']);
        move_uploaded_file($_FILES['proof_image']['tmp_name'], $uploadDir . $proof_image);
    }

    $itemModel->createClaim($item_id, $claimant_name, $phone, $message, $proof_image);

    $claim_success = true;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Item Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <a href="index.php" class="btn btn-secondary mb-3">⬅ Back</a>

    <div class="card">
        <div class="card-body">

            <h3><?= htmlspecialchars($item['title']) ?></h3>

            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" class="img-fluid mb-3" style="max-height:300px; object-fit:cover;">

            <p><strong>Category:</strong> <?= htmlspecialchars($item['category_name']) ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($item['item_date']) ?></p>
            <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>

            <hr>

            <?php if ($item['type'] === 'found'): ?>

                <?php if ($item['status'] === 'returned'): ?>
                    <div class="alert alert-info">
                        <strong>Returned:</strong> This item has been returned to 
                        <strong><?= htmlspecialchars($approvedClaim['claimant_name'] ?? "the rightful owner") ?></strong>.
                    </div>

                <?php else: ?>

                    <h4>Claim This Item</h4>

                    <?php if ($claim_success): ?>
                        <div class="alert alert-success">Your claim has been submitted. Admin will contact you.</div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Your Name</label>
                            <input type="text" name="claimant_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Message</label>
                            <textarea name="message" class="form-control" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Upload Proof (optional)</label>
                            <input type="file" name="proof_image" class="form-control">
                        </div>

                        <button name="claim_submit" class="btn btn-success">Submit Claim</button>
                    </form>

                <?php endif; ?>

            <?php else: ?>
                <div class="alert alert-warning">This is a LOST item. Claim option only appears on FOUND items.</div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
