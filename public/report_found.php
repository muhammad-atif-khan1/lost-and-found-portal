<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';

$itemModel = new ItemModel($pdo);

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = null; // No login system
    $type = "found";
    $category_id = $_POST['category_id'];
    $item_date = $_POST['item_date'];
    $location = $_POST['location'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // ---- IMAGE UPLOAD ----
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {

        $uploadDir = __DIR__ . '/../uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $target = $uploadDir . $imageName;

        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // SAVE ITEM
    $itemModel->create(
        $user_id,      // null
        $type,         // found
        $category_id,
        $item_date,
        $location,
        $title,
        $description,
        $imageName
    );

    header("Location: index.php?success=found");
    exit;
}

// Load categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();

include __DIR__ . '/../views/partials/header.php';
?>

<div class="container mt-4">
    <h2 class="text-success">📦 Report a Found Item</h2>

    <form method="POST" enctype="multipart/form-data" class="mt-3">

        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['category_id'] ?>"><?= $c['category_name'] ?></option>
            <?php endforeach; ?>
        </select>

        <label class="mt-3">Date Found</label>
        <input type="date" name="item_date" class="form-control" required>

        <label class="mt-3">Location Found</label>
        <input type="text" name="location" class="form-control" required>

        <label class="mt-3">Item Title</label>
        <input type="text" name="title" class="form-control" required>

        <label class="mt-3">Description</label>
        <textarea name="description" class="form-control" rows="4" required></textarea>

        <label class="mt-3">Upload Image</label>
        <input type="file" name="image" class="form-control">

        <button class="btn btn-success mt-4">Submit Found Report</button>
    </form>
</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
