<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Area'; ?></title> 

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="../css/admin_styles.css">

    <style>
        /* Ensures the table header for manage pages is colored consistently */
        .table-admin thead {
            background-color: #3a0ca3;
            color: white;
        }
        /* Sets a clean background for pages that aren't the main dashboard */
        body {
            background-color: #f8f9fa; 
        }
    </style>
</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #000428; background-image: linear-gradient(135deg, #000428 0%, #004e92 100%); border-bottom: 3px solid #4cc9f0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);">
    <div class="container-fluid mx-lg-5">
        
        <a class="navbar-brand text-white d-flex align-items-center" href="admin_dashboard.php" style="font-size: 1.8rem; font-weight: 700;">
            <i class="fas fa-crown me-2" style="color: #FFD700;"></i> 
            <span style="letter-spacing: 1px;">Admin Console</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" style="border-color: rgba(255, 255, 255, 0.5);">
            <i class="fas fa-bars text-white"></i>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            
           

            <ul class="navbar-nav ms-auto align-items-center">

                <li class="nav-item">
                    <a class="nav-link mx-2 text-warning" href="manage_items.php" style="font-weight: 600;">
                        <i class="fas fa-clipboard-list me-1"></i> Manage Items
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mx-2 text-warning" href="../admin/manage_claims.php" style="font-weight: 600;">
                        <i class="fas fa-check-circle me-1"></i> Claims
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link mx-2 text-warning" href="manage_categories.php" style="font-weight: 600;">
                        <i class="fas fa-tags me-1"></i> Categories
                    </a>
                </li>
                
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-danger px-4 py-2" href="admin_logout.php" style="background-color: #dc3545; border: none; font-weight: 600; box-shadow: 0 2px 5px rgba(220, 53, 69, 0.4);">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">