<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';

$itemModel = new ItemModel($pdo);

// Load categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll();

// Filters (optional search)
$filters = [];
if (!empty($_GET['type'])) $filters['type'] = $_GET['type'];
if (!empty($_GET['category_id'])) $filters['category_id'] = $_GET['category_id'];
if (!empty($_GET['location'])) $filters['location'] = $_GET['location'];

$items = $itemModel->search($filters, 50, 0);

// Load header
include __DIR__ . '/../views/partials/header.php';
?>



<?php
// Load home content
include __DIR__ . '/../views/home.php';

// Load footer
include __DIR__ . '/../views/partials/footer.php';
?>
