<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$user_name = $_SESSION['name'];

// Get stats
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$total_revenue = $conn->query("SELECT SUM(price) as t FROM orders")->fetch_assoc()['t'] ?? 0;
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$completed = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='completed'")->fetch_assoc()['c'];
$avg = $total_orders > 0 ? $total_revenue / $total_orders : 0;

// Top items
$top = $conn->query("SELECT item_name, COUNT(*) as c FROM orders GROUP BY item_name ORDER BY c DESC LIMIT 5");

// Recent orders
$recent = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 10");

// Weekly sales data
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
$weekly_sales = [18500, 22400, 19800, 25600, 31200];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/reports.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <a href="orders.php" class="nav-item"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending>0) echo "<span class='badge'>$pending</span>"; ?></a>
            <a href="menu.php" class="nav-item"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
            <a href="tables.php" class="nav-item"><i class="fas fa-chair"></i><span>Tables</span></a>
            <a href="staff.php" class="nav-item"><i class="fas fa-users"></i><span>Staff</span></a>
            <a href="customers.php" class="nav-item"><i class="fas fa-user-friends"></i><span>Customers</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item active"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small>Admin</small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar"><div><h1>Reports</h1><p>Business analytics</p></div><div class="date"><?php echo date('F j, Y'); ?></div></div>

        <div class="stats-row">
            <div class="stat"><i class="fas fa-shopping-cart"></i><div><h3><?php echo $total_orders; ?></h3><p>Total Orders</p></div></div>
            <div class="stat"><i class="fas fa-chart-line"></i><div><h3>Ksh <?php echo number_format($total_revenue); ?></h3><p>Total Revenue</p></div></div>
            <div class="stat"><i class="fas fa-calculator"></i><div><h3>Ksh <?php echo number_format($avg); ?></h3><p>Average Order</p></div></div>
            <div class="stat"><i class="fas fa-clock"></i><div><h3><?php echo $pending; ?></h3><p>Pending Orders</p></div></div>
        </div>

        <div class="charts">
            <div class="chart-box">
                <div class="title"><i class="fas fa-chart-line"></i> Weekly Sales <span>Mon - Fri</span></div>
                <canvas id="weeklyChart" class="chart-canvas" height="180"></canvas>
            </div>
            <div class="top-box">
                <div class="title"><i class="fas fa-fire"></i> Top Selling Items</div>
                <?php $rank=1; while($item=$top->fetch_assoc()): ?>
                <div class="item"><span class="rank">#<?php echo $rank++; ?></span><span><?php echo $item['item_name']; ?></span><span class="count"><?php echo $item['c']; ?></span></div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="recent">
            <div class="title"><i class="fas fa-history"></i> Recent Orders</div>
            <div class="table-wrap">
                <table class="orders-table">
                    <thead><tr><th>ID</th><th>Table</th><th>Item</th><th>Price</th><th>Status</th></tr></thead>
                    <tbody><?php while($row=$recent->fetch_assoc()): ?><tr><td class="order-id">#<?php echo $row['id']; ?></td><td>Table <?php echo $row['table_number']; ?></td><td><?php echo $row['item_name']; ?></td><td class="price">Ksh <?php echo $row['price']; ?></td><td><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td></tr><?php endwhile; ?></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="../js/reports.js"></script>
</body>
</html>