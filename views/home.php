<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/ItemModel.php';

$itemModel = new ItemModel($pdo);

// load categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// shared filters
$filters_base = [];
if (!empty($_GET['category_id'])) $filters_base['category_id'] = (int)$_GET['category_id'];
if (!empty($_GET['location'])) $filters_base['location'] = trim($_GET['location']);

$limit = 6;

// LOST pagination
$lost_page = isset($_GET['lost_page']) ? max(1, (int)$_GET['lost_page']) : 1;
$lost_offset = ($lost_page - 1) * $limit;
$lost_filters = $filters_base;
$lost_filters['type'] = 'lost';
$lost_items = $itemModel->search($lost_filters, $limit, $lost_offset);

// FOUND pagination
$found_page = isset($_GET['found_page']) ? max(1, (int)$_GET['found_page']) : 1;
$found_offset = ($found_page - 1) * $limit;
$found_filters = $filters_base;
$found_filters['type'] = 'found';
$found_items = $itemModel->search($found_filters, $limit, $found_offset);

function preserve_query($overrides = []) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Lost & Found</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-img-top { height:180px; object-fit:cover; }
    .badge-returned { background:#6c757d; }
  </style>
</head>

<body>

<div class="container mt-4">
  <h2 class="text-center mb-4 text-primary">Lost &amp; Found</h2>

  <!-- Filters -->
  <form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
      <select name="category_id" class="form-select">
        <option value="">All categories</option>
        <?php foreach($categories as $c): ?>
          <option value="<?= $c['category_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id']==$c['category_id'])?'selected':'' ?>>
            <?= htmlspecialchars($c['category_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-4">
      <input name="location" class="form-control" placeholder="Location (optional)" value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
    </div>

    <div class="col-md-2">
      <button class="btn btn-primary w-100">Filter</button>
    </div>
  </form>


  <div class="row">

    <!-- LOST COLUMN -->
    <div class="col-md-6">
      <h4 class="text-danger">🔴 Lost Items</h4>
      <hr>

      <?php if (empty($lost_items)): ?>
        <p class="text-muted">No lost items found.</p>
      <?php else: ?>
        <?php foreach ($lost_items as $item): ?>
          <div class="card mb-3">
            <?php $img = !empty($item['image']) ? $item['image'] : 'no-image.png'; ?>
            <img src="../uploads/<?= htmlspecialchars($img) ?>" class="card-img-top">

            <div class="card-body">
              <span class="badge bg-danger mb-2">Lost</span>
              <?php if (!empty($item['status']) && $item['status'] === 'returned'): ?>
                <span class="badge badge-returned mb-2">Returned</span>
              <?php endif; ?>

              <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
              <p class="small text-muted mb-1"><strong>Category:</strong> <?= htmlspecialchars($item['category_name']) ?></p>
              <p class="small text-muted mb-1"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
              <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>

              <!-- FIXED LINK -->
              <a href="item.php?id=<?= $item['item_id'] ?>" class="btn btn-sm btn-outline-primary">Details</a>

            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Pagination -->
      <nav>
        <ul class="pagination">
          <li class="page-item <?= $lost_page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= preserve_query(['lost_page' => $lost_page - 1]) ?>">Prev</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="?<?= preserve_query(['lost_page' => $lost_page + 1]) ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>


    <!-- FOUND COLUMN -->
    <div class="col-md-6">
      <h4 class="text-success">🟢 Found Items</h4>
      <hr>

      <?php if (empty($found_items)): ?>
        <p class="text-muted">No found items found.</p>
      <?php else: ?>
        <?php foreach ($found_items as $item): ?>
          <div class="card mb-3">
            <?php $img = !empty($item['image']) ? $item['image'] : 'no-image.png'; ?>
            <img src="../uploads/<?= htmlspecialchars($img) ?>" class="card-img-top">

            <div class="card-body">
              <span class="badge bg-success mb-2">Found</span>
              <?php if (!empty($item['status']) && $item['status'] === 'returned'): ?>
                <span class="badge badge-returned mb-2">Returned</span>
              <?php endif; ?>

              <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
              <p class="small text-muted mb-1"><strong>Category:</strong> <?= htmlspecialchars($item['category_name']) ?></p>
              <p class="small text-muted mb-1"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
              <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>

              <!-- FIXED LINK -->
              <a href="item.php?id=<?= $item['item_id'] ?>" class="btn btn-sm btn-outline-success">View / Claim</a>

            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Pagination -->
      <nav>
        <ul class="pagination">
          <li class="page-item <?= $found_page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= preserve_query(['found_page' => $found_page - 1]) ?>">Prev</a>
          </li>
          <li class="page-item">
            <a class="page-link" href="?<?= preserve_query(['found_page' => $found_page + 1]) ?>">Next</a>
          </li>
        </ul>
      </nav>
    </div>

  </div>
</div>

</body>
</html>
