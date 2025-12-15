<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$id = $_GET['id'];

// Get item data
$stmt = $pdo->prepare("SELECT * FROM items WHERE item_id=?");
$stmt->execute([$id]);
$item = $stmt->fetch();

// Update item
$message = "";
if (isset($_POST['update'])) {

    $title = $_POST['title'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare("UPDATE items 
                           SET title=?, location=?, description=? 
                           WHERE item_id=?");
    $stmt->execute([$title, $location, $description, $id]);

    $message = "Item updated successfully!";
}

?>

<?php include __DIR__ . '/../views/partials/header.php'; ?>

<div class="container mt-5">
    <h2>✏ Edit Item</h2>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="p-4 shadow bg-light">

        <label>Title</label>
        <input type="text" name="title" value="<?= $item['title'] ?>" class="form-control" required>

        <label class="mt-3">Location</label>
        <input type="text" name="location" value="<?= $item['location'] ?>" class="form-control" required>

        <label class="mt-3">Description</label>
        <textarea name="description" class="form-control" rows="4"><?= $item['description'] ?></textarea>

        <button name="update" class="btn btn-primary w-100 mt-4">Update</button>
    </form>
</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
