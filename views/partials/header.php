<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .nav-link {
            font-size: 1.1rem;
        }
        .btn-report {
            border-radius: 20px;
            padding: 6px 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <!-- Site Name -->
        <a class="navbar-brand" href="index.php">
            🔍 Lost & Found
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Items -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-center">

               

                <!-- Report Lost -->
                <li class="nav-item">
                    <a class="btn btn-danger btn-report ms-2" href="report_lost.php">
                        ➕ Report Lost Item
                    </a>
                </li>

                
                <!-- Report Found -->
                <li class="nav-item">
                    <a class="btn btn-success btn-report ms-2" href="report_found.php">
                        📤 Report Found Item
                    </a>
                </li>

                <!-- Returned Items -->
                <li class="nav-item ms-3">
                    <a class="nav-link" href="returned_items.php">🔁 Returned Items</a>
                </li>

                <!-- Admin Login -->
                <li class="nav-item">
                    <a class="btn btn-warning text-dark ms-3 px-3" href="admin_login.php">
                        🔐 Admin Login
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>


