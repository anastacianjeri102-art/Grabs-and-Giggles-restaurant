<?php
// Check if user is logged in (for pages that need it)
if(!isset($skip_auth) && !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user info if logged in
$user_name = $_SESSION['name'] ?? 'Guest';
$user_role = $_SESSION['role'] ?? 'Guest';
$pending_count = 0;

// Get pending count for badge if database connection exists
if(isset($conn) && isset($_SESSION['user_id'])) {
    $pending_result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    if($pending_result) {
        $pending_count = $pending_result->fetch_assoc()['total'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Grabs & Giggles'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <?php if(isset($extra_css)): ?>
        <link rel="stylesheet" href="../css/<?php echo $extra_css; ?>.css">
    <?php endif; ?>
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR - Navigation for all pages -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <span>Grabs & Giggles</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Dashboard - Only for Admin -->
            <?php if($user_role == 'Admin'): ?>
                <a href="dashboard.php" class="nav-item <?php echo ($active_page ?? '') == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>
            
            <!-- Orders - All users except Kitchen? Kitchen has separate view -->
            <?php if($user_role != 'Kitchen'): ?>
                <a href="orders.php" class="nav-item <?php echo ($active_page ?? '') == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Orders</span>
                    <?php if($pending_count > 0): ?>
                        <span class="badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            
            <!-- Kitchen Display - For Kitchen and Admin -->
            <?php if($user_role == 'Kitchen' || $user_role == 'Admin'): ?>
                <a href="kitchen.php" class="nav-item <?php echo ($active_page ?? '') == 'kitchen' ? 'active' : ''; ?>">
                    <i class="fas fa-utensils"></i>
                    <span>Kitchen</span>
                    <?php if($pending_count > 0): ?>
                        <span class="badge"><?php echo $pending_count; ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            
            <!-- Menu - For Waiters and Admin -->
            <?php if($user_role == 'Waiter' || $user_role == 'Admin'): ?>
                <a href="menu.php" class="nav-item <?php echo ($active_page ?? '') == 'menu' ? 'active' : ''; ?>">
                    <i class="fas fa-utensil-spoon"></i>
                    <span>Menu</span>
                </a>
            <?php endif; ?>
            
            <!-- Tables - For All Users -->
            <a href="tables.php" class="nav-item <?php echo ($active_page ?? '') == 'tables' ? 'active' : ''; ?>">
                <i class="fas fa-chair"></i>
                <span>Tables</span>
            </a>
            
            <!-- Staff - Only for Admin -->
            <?php if($user_role == 'Admin'): ?>
                <a href="staff.php" class="nav-item <?php echo ($active_page ?? '') == 'staff' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Staff</span>
                </a>
            <?php endif; ?>
            
            <!-- Customers - Only for Admin -->
            <?php if($user_role == 'Admin'): ?>
                <a href="customers.php" class="nav-item <?php echo ($active_page ?? '') == 'customers' ? 'active' : ''; ?>">
                    <i class="fas fa-user-friends"></i>
                    <span>Customers</span>
                </a>
            <?php endif; ?>
            
            <!-- Inventory - Only for Admin -->
            <?php if($user_role == 'Admin'): ?>
                <a href="inventory.php" class="nav-item <?php echo ($active_page ?? '') == 'inventory' ? 'active' : ''; ?>">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
            <?php endif; ?>
            
            <!-- Reports - Only for Admin -->
            <?php if($user_role == 'Admin'): ?>
                <a href="reports.php" class="nav-item <?php echo ($active_page ?? '') == 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            <?php endif; ?>
            
            <!-- Logout - For All Users -->
            <a href="logout.php" class="nav-item logout" onclick="return confirm('Are you sure you want to logout?')">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
        
        <div class="user-info-sidebar">
            <i class="fas fa-user-circle"></i>
            <div>
                <p><?php echo htmlspecialchars($user_name); ?></p>
                <small><?php echo $user_role; ?></small>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT START -->
    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1><?php echo $page_heading ?? 'Dashboard'; ?></h1>
                <p>Welcome, <?php echo htmlspecialchars($user_name); ?></p>
            </div>
        </div>