<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Activate / deactivate user
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $pdo->query("UPDATE users SET status = IF(status=1,0,1) WHERE id=$id");
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>

<?php include __DIR__ . '/../views/partials/admin_header.php'; ?>

<div class="container mt-5">
    <h2>👥 Manage Users</h2>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Toggle</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= $u['name'] ?></td>
                    <td><?= $u['email'] ?></td>
                    <td><?= ucfirst($u['role']) ?></td>
                    <td><?= $u['status'] ? 'Active' : 'Blocked' ?></td>
                    <td>
                        <a href="?toggle=<?= $u['id'] ?>" class="btn btn-warning btn-sm">
                            <?= $u['status'] ? 'Deactivate' : 'Activate' ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

<?php include __DIR__ . '/../views/partials/footer.php'; ?>
