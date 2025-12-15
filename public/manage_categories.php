<?php
session_start();

// CRITICAL FIX: These headers instruct the browser and proxies NOT to cache the page content.
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.



if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Add new category
$message = "";
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);

    if ($name != "") {
        $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$name]);
        $message = "Category added!";
    }
}

// Get all categories
$cats = $pdo->query("SELECT * FROM categories ORDER BY category_id DESC")->fetchAll();

?>

<?php include __DIR__ . '/../views/partials/admin_header.php'; ?>

<div class="container mt-5">

    <h2 class="mb-3">📂 Manage Categories</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="d-flex mb-4">
        <input type="text" name="category_name" class="form-control me-2" placeholder="New Category" required>
        <button class="btn btn-primary" name="add_category">Add</button>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Category Name</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($cats as $c): ?>
                <tr>
                    <td><?= $c['category_id'] ?></td>
                    <td><?= $c['category_name'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
