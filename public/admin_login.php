<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // admin login (no hashing as you requested)
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND password=? AND role='admin'");
    $stmt->execute([$email, $password]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin'] = $admin['id'];
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $message = "Invalid credentials!";
    }
}
?>

<?php include __DIR__ . '/../views/partials/header.php'; ?>

<div class="container mt-5" style="max-width: 450px;">
    <h3 class="mb-3 text-center">👤 Admin Login</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="p-4 shadow rounded bg-light">

        <label>Email</label>
        <input type="email" name="email" class="form-control" required>

        <label class="mt-3">Password</label>
        <input type="password" name="password" class="form-control" required>

        <button class="btn btn-primary w-100 mt-4">Login</button>
    </form>
</div>


