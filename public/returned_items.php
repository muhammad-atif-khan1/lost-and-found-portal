<?php
require_once __DIR__ . '/../config/db.php';

// Fetch only APPROVED claims
$stmt = $pdo->prepare("
    SELECT 
        c.claim_id,
        c.claimant_name,
        c.phone,
        c.message,
        c.proof_image,
        c.status,
        c.created_at,
        i.title,
        i.description,
        i.image
    FROM claims c
    JOIN items i ON c.item_id = i.item_id
    WHERE c.status = 'approved'
    ORDER BY c.created_at DESC
");
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../views/partials/header.php';
?>

<div class="container mt-4">
    <h3>Returned Items</h3>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Item</th>
                <th>Image</th>
                <th>Claimant</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Returned On</th>
            </tr>
        </thead>

        <tbody>
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>

                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" height="60">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($item['claimant_name']) ?></td>
                    <td><?= htmlspecialchars($item['phone']) ?></td>
                    <td><?= nl2br(htmlspecialchars($item['message'])) ?></td>
                    <td><?= htmlspecialchars($item['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No returned items yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>


