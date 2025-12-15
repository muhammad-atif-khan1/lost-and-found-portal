<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// filter (pending / approved / rejected / all)
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';
$allowed = ['pending', 'approved', 'rejected', 'all'];
if (!in_array($filter, $allowed)) $filter = 'pending';

// Fetch claims joined with item and category info, ordered by item then newest claim
if ($filter === 'all') {
    $sql = "
      SELECT 
        c.*,
        i.item_id AS item_id,
        i.title AS item_title,
        i.image AS item_image,
        i.type AS item_type,
        i.location AS item_location,
        i.item_date AS item_date,
        cat.category_name
      FROM claims c
      JOIN items i ON c.item_id = i.item_id
      LEFT JOIN categories cat ON i.category_id = cat.category_id
      ORDER BY i.item_id ASC, c.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} else {
    $sql = "
      SELECT 
        c.*,
        i.item_id AS item_id,
        i.title AS item_title,
        i.image AS item_image,
        i.type AS item_type,
        i.location AS item_location,
        i.item_date AS item_date,
        cat.category_name
      FROM claims c
      JOIN items i ON c.item_id = i.item_id
      LEFT JOIN categories cat ON i.category_id = cat.category_id
      WHERE c.status = ?
      ORDER BY i.item_id ASC, c.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$filter]);
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group rows by item_id
$grouped = [];
foreach ($rows as $r) {
    $iid = $r['item_id'];
    if (!isset($grouped[$iid])) {
        $grouped[$iid] = [
            'item' => [
                'item_id' => $iid,
                'title' => $r['item_title'],
                'image' => $r['item_image'],
                'type' => $r['item_type'],
                'location' => $r['item_location'],
                'item_date' => $r['item_date'],
                'category_name' => $r['category_name']
            ],
            'claims' => []
        ];
    }
    $grouped[$iid]['claims'][] = $r;
}

// Helper to build query preserving filter
function build_q($overrides = []) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}

include __DIR__ . '/../views/partials/admin_header.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="mb-0">📌 Manage Claims</h3>

        <div>
            <!-- Filter buttons -->
            <a href="?<?= build_q(['filter' => 'pending']) ?>" class="btn btn-outline-primary <?= $filter === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?<?= build_q(['filter' => 'approved']) ?>" class="btn btn-outline-success <?= $filter === 'approved' ? 'active' : '' ?>">Approved</a>
            <a href="?<?= build_q(['filter' => 'rejected']) ?>" class="btn btn-outline-danger <?= $filter === 'rejected' ? 'active' : '' ?>">Rejected</a>
            <a href="?<?= build_q(['filter' => 'all']) ?>" class="btn btn-outline-secondary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
        </div>
    </div>

    <?php if (empty($grouped)): ?>
        <div class="alert alert-info">No claims found for the selected filter.</div>
    <?php else: ?>

        <?php foreach ($grouped as $gid => $block): 
            $item = $block['item'];
            $claims = $block['claims'];

            // Count pending claims for warning
            $pendingCount = 0;
            foreach ($claims as $c) if ($c['status'] === 'pending') $pendingCount++;
        ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center">
                    <div class="me-3">
                        <?php if (!empty($item['image']) && file_exists(__DIR__ . '/../uploads/' . $item['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" alt="item" style="width:80px;height:60px;object-fit:cover;border-radius:6px;">
                        <?php else: ?>
                            <div style="width:80px;height:60px;background:#f1f1f1;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#888;">No Img</div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-grow-1">
                        <h5 class="mb-0"><?= htmlspecialchars($item['title']) ?></h5>
                        <small class="text-muted">
                            <?= htmlspecialchars($item['category_name'] ?? 'Uncategorized') ?> • 
                            <?= $item['type'] === 'found' ? '<span class="text-success">Found</span>' : '<span class="text-danger">Lost</span>' ?>
                            • <?= htmlspecialchars($item['location'] ?? '-') ?> 
                            • <?= htmlspecialchars($item['item_date'] ?? '-') ?>
                        </small>
                    </div>

                    <div class="text-end">
                        <?php if ($pendingCount > 1): ?>
                            <span class="badge bg-warning text-dark">⚠ <?= $pendingCount ?> pending claims</span>
                        <?php elseif ($pendingCount === 1): ?>
                            <span class="badge bg-info text-dark">1 pending claim</span>
                        <?php endif; ?>

                        <a href="process_all_claims.php?item_id=<?= $item['item_id'] ?>" class="btn btn-sm btn-outline-secondary ms-2" title="View item">Open</a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:14%">Claimant</th>
                                    <th style="width:12%">Phone</th>
                                    <th style="width:34%">Message</th>
                                    <th style="width:12%">Proof</th>
                                    <th style="width:10%">Status</th>
                                    <th style="width:18%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($claims as $c): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($c['claimant_name']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($c['created_at']) ?></small></td>
                                        <td><?= htmlspecialchars($c['phone']) ?></td>
                                        <td style="white-space:pre-wrap;"><?= nl2br(htmlspecialchars($c['message'])) ?></td>
                                        <td>
                                            <?php if (!empty($c['proof_image']) && file_exists(__DIR__ . '/../uploads/claims/' . $c['proof_image'])): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#proofModal" data-image="../uploads/claims/<?= rawurlencode($c['proof_image']) ?>">
                                                    View Proof
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">No proof</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($c['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif ($c['status'] === 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($c['status'] === 'pending'): ?>
                                                <!-- Approve: confirm and go to process_claim.php -->
                                                <a href="process_claim.php?id=<?= (int)$c['claim_id'] ?>&action=approve" 
                                                   onclick="return confirm('Approve this claim? Approving will mark the item as returned and reject other claims.');"
                                                   class="btn btn-sm btn-success">Approve</a>

                                                <!-- Reject -->
                                                <a href="process_claim.php?id=<?= (int)$c['claim_id'] ?>&action=reject" 
                                                   onclick="return confirm('Reject this claim?');"
                                                   class="btn btn-sm btn-danger">Reject</a>
                                            <?php else: ?>
                                                <span class="text-muted">No action</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->
                </div> <!-- card-body -->
            </div> <!-- card -->
        <?php endforeach; ?>

    <?php endif; ?>
</div>

<!-- Proof modal -->
<div class="modal fade" id="proofModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img src="" id="proofImage" style="width:100%;height:auto;display:block;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS (required for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var proofModal = document.getElementById('proofModal');
    proofModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var img = button.getAttribute('data-image');
        document.getElementById('proofImage').setAttribute('src', img);
    });
});
</script>


