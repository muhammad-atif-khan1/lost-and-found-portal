<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';

$itemModel = new ItemModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $type = "lost";
    $category_id = $_POST['category_id'];
    $item_date = $_POST['item_date'];
    $location = $_POST['location'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // IMAGE UPLOAD
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

    // SAVE ITEM (NO user_id, auto-approved)
    $itemModel->create(
        null,
        $type,
        $category_id,
        $item_date,
        $location,
        $title,
        $description,
        $imageName
    );

    header("Location: index.php?success=lost");
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

// Include header
include __DIR__ . '/../views/partials/header.php';
?>

<div class="container mt-5">
    <h2 class="mb-3">Report Lost Item</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-control" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>">
                        <?= $cat['category_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Date Lost</label>
            <input type="date" name="item_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button class="btn btn-primary">Submit Report</button>
    </form>
</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
