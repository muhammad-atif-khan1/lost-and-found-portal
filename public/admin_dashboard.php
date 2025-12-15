<?php
// Start the session at the very beginning
session_start();

// CRITICAL FIX: These headers instruct the browser and proxies NOT to cache the page content.
// This prevents the "back button still shows admin page" issue after logging out.
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// 1. Authentication Check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Database Connection
require_once __DIR__ . '/../config/db.php';

// Count stats
$lostCount = $pdo->query("SELECT COUNT(*) FROM items WHERE type='lost'")->fetchColumn();
$foundCount = $pdo->query("SELECT COUNT(*) FROM items WHERE type='found'")->fetchColumn();
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pendingClaims = $pdo->query("SELECT COUNT(*) FROM claims WHERE status='pending'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
            --dark-color: #212529;
            --light-color: #f8f9fa;
        }

        .dashboard-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            padding: 20px;
        }

        .dashboard-header {
            background: white;
            border-radius: 15px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--primary-color);
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .stat-card.lost::before {
            background: linear-gradient(90deg, #ff6b6b, #ff8e8e);
        }

        .stat-card.found::before {
            background: linear-gradient(90deg, #4cc9f0, #4361ee);
        }

        .stat-card.users::before {
            background: linear-gradient(90deg, #7209b7, #3a0ca3);
        }

        .stat-card.pending::before {
            background: linear-gradient(90deg, #f72585, #ff4d6d);
        }

        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-card .stat-number {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 10px 0;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Quick Actions Section */
        .action-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 25px 15px;
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            text-align: center;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .action-btn i {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        .action-btn .btn-label {
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        /* Pending Badge */
        .badge-pending {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(45deg, #f72585, #ff4d6d);
            color: white;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            min-width: 25px;
            box-shadow: 0 3px 10px rgba(247, 37, 133, 0.3);
        }

        .logout-btn {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }
            .stat-card {
                padding: 20px 15px;
            }
            .stat-card .stat-number {
                font-size: 2.2rem;
            }
            .action-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>

<body>

<?php 
// 4. INCLUDE THE ADMIN HEADER (Your custom navigation bar)
include __DIR__ . '/../views/partials/admin_header.php'; 
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">📊 Admin Dashboard</h1>
                <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!</p>
            </div>
            <div class="text-end">
                <small class="text-muted">Last login: Today, <?php echo date('H:i'); ?></small>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card lost">
                <div class="icon">🔍</div>
                <div class="stat-number"><?= $lostCount ?></div>
                <div class="stat-label">Lost Items</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card found">
                <div class="icon">📦</div>
                <div class="stat-number"><?= $foundCount ?></div>
                <div class="stat-label">Found Items</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card users">
                <div class="icon">👥</div>
                <div class="stat-number"><?= $userCount ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card pending">
                <div class="icon">⏳</div>
                <div class="stat-number"><?= $pendingClaims ?></div>
                <div class="stat-label">Pending Claims</div>
            </div>
        </div>
    </div>

    <div class="action-section">
        <h3 class="section-title">🚀 Quick Actions</h3>
        
        <div class="action-grid mb-4">
            <a href="manage_items.php" class="action-btn">
                <i>📋</i>
                <span class="btn-label">Manage Items</span>
            </a>
            
            <a href="manage_categories.php" class="action-btn">
                <i>🏷️</i>
                <span class="btn-label">Manage Categories</span>
            </a>
            
            <a href="../admin/manage_claims.php" class="action-btn position-relative">
                <i>✅</i>
                <span class="btn-label">Manage Claims</span>
                <?php if ($pendingClaims > 0): ?>
                    <span class="badge-pending"><?= $pendingClaims ?> pending</span>
                <?php endif; ?>
            </a>
            
            <a href="manage_users.php" class="action-btn">
                <i>👤</i>
                <span class="btn-label">Manage Users</span>
            </a>
        </div>
        
        
    </div>
</div>
<?php 
// 7. INCLUDE FOOTER
include __DIR__ . '/../views/partials/footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>