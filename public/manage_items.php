<?php
session_start();

// CRITICAL FIX: These headers instruct the browser and proxies NOT to cache the page content.
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// 1. Authentication Check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Fetch all items
$stmt = $pdo->query("
    SELECT i.*, c.category_name
    FROM items i
    JOIN categories c ON i.category_id = c.category_id
    ORDER BY i.created_at DESC
");
$items = $stmt->fetchAll();

// Fetch all approved claims (to show Returned To)
$claimStmt = $pdo->query("
    SELECT item_id, claimant_name 
    FROM claims 
    WHERE status = 'approved'
");
$approvedClaims = [];

foreach ($claimStmt->fetchAll() as $c) {
    $approvedClaims[$c['item_id']] = $c['claimant_name'];
}

?>

<?php include __DIR__ . '/../views/partials/admin_header.php'; ?>

<div class="container mt-5">
    <h2 class="mb-3">📝 Manage Items</h2>

    <table class="table table-bordered table-striped mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= $item['item_id'] ?></td>

                <td>
                    <?= $item['type'] === 'lost' ? '🔴 Lost' : '🟢 Found' ?>
                </td>

                <td><?= htmlspecialchars($item['title']) ?></td>

                <td><?= htmlspecialchars($item['category_name']) ?></td>

                <td>
                    <?php if ($item['status'] === 'returned'): ?>

                        <span class="badge bg-info text-dark">Returned</span>

                        <?php if (isset($approvedClaims[$item['item_id']])): ?>
                            <br>
                            <small class="text-primary">
                                Returned to: <b><?= htmlspecialchars($approvedClaims[$item['item_id']]) ?></b>
                            </small>
                        <?php endif; ?>

                    <?php elseif ($item['status'] === 'pending'): ?>

                        <span class="badge bg-warning text-dark">Pending</span>

                    <?php elseif ($item['status'] === 'approved'): ?>

                        <span class="badge bg-success">Approved</span>

                    <?php else: ?>

                        <span class="badge bg-danger">Rejected</span>

                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($item['image']): ?>
                        <img src="../uploads/<?= $item['image'] ?>" width="60" height="60" style="object-fit:cover;border-radius:5px;">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>

                <td class="text-center">
                    <a href="approve_item.php?id=<?= $item['item_id'] ?>" class="btn btn-success btn-sm mb-1">Approve</a>
                    <a href="reject_item.php?id=<?= $item['item_id'] ?>" class="btn btn-warning btn-sm mb-1">Reject</a>

                    <a href="delete_item.php?id=<?= $item['item_id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Are you sure you want to permanently delete this item?');">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


