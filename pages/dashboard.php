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
$staff = $conn->query("SELECT COUNT(*) as c FROM staff")->fetch_assoc()['c'];
$tables = $conn->query("SELECT COUNT(*) as c FROM restaurant_tables")->fetch_assoc()['c'];
$customers = $conn->query("SELECT COUNT(*) as c FROM customers")->fetch_assoc()['c'];
$inventory = $conn->query("SELECT COUNT(*) as c FROM inventory")->fetch_assoc()['c'];
$low_stock = $conn->query("SELECT COUNT(*) as c FROM inventory WHERE quantity <= reorder_level")->fetch_assoc()['c'];

// Recent orders
$recent = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");

// Top items
$top = $conn->query("SELECT item_name, COUNT(*) as count FROM orders GROUP BY item_name ORDER BY count DESC LIMIT 5");

// Weekly sales
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
$sales = [18500, 22400, 19800, 25600, 31200];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <a href="orders.php" class="nav-item"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending>0) echo "<span class='badge'>$pending</span>"; ?></a>
            <a href="menu.php" class="nav-item"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
            <a href="tables.php" class="nav-item"><i class="fas fa-chair"></i><span>Tables</span></a>
            <a href="staff.php" class="nav-item"><i class="fas fa-users"></i><span>Staff</span></a>
            <a href="customers.php" class="nav-item"><i class="fas fa-user-friends"></i><span>Customers</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small>Admin</small></div></div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <div><h1>Dashboard</h1><p>Welcome, <?php echo $user_name; ?></p></div>
            <div class="date"><i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y'); ?></div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card"><i class="fas fa-shopping-cart"></i><div><h3><?php echo $total_orders; ?></h3><p>Total Orders</p><span>↑12%</span></div></div>
            <div class="stat-card"><i class="fas fa-chart-line"></i><div><h3>Ksh <?php echo number_format($total_revenue); ?></h3><p>Revenue</p><span>↑8%</span></div></div>
            <div class="stat-card"><i class="fas fa-clock"></i><div><h3><?php echo $pending; ?></h3><p>Pending</p><span>↓3%</span></div></div>
            <div class="stat-card"><i class="fas fa-check-circle"></i><div><h3><?php echo $completed; ?></h3><p>Completed</p><span>↑15%</span></div></div>
        </div>

        <!-- Quick Info -->
        <div class="quick-row">
            <div class="quick-card"><i class="fas fa-users"></i><div><h4><?php echo $staff; ?></h4><p>Staff</p></div></div>
            <div class="quick-card"><i class="fas fa-chair"></i><div><h4><?php echo $tables; ?></h4><p>Tables</p></div></div>
            <div class="quick-card"><i class="fas fa-user-friends"></i><div><h4><?php echo $customers; ?></h4><p>Customers</p></div></div>
            <div class="quick-card"><i class="fas fa-boxes"></i><div><h4><?php echo $inventory; ?></h4><p>Items</p><small><?php echo $low_stock; ?> low</small></div></div>
        </div>

        <!-- Charts -->
        <div class="charts-row">
            <div class="chart-box">
                <div class="box-title"><i class="fas fa-chart-line"></i> Weekly Sales <span>Mon-Fri</span></div>
                <canvas id="salesChart" height="150"></canvas>
            </div>
            <div class="top-box">
                <div class="box-title"><i class="fas fa-fire"></i> Best Sellers</div>
                <?php $rank=1; while($item=$top->fetch_assoc()): ?>
                <div class="top-item"><span class="rank">#<?php echo $rank++; ?></span><span><?php echo $item['item_name']; ?></span><span class="count"><?php echo $item['count']; ?></span></div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Recent Orders - FIXED TABLE -->
        <div class="recent-box">
            <div class="box-title">
                <h3><i class="fas fa-history"></i> Recent Orders</h3>
                <a href="orders.php" class="view-link">View All →</a>
            </div>
            <div class="table-wrap">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Table</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $recent->fetch_assoc()): ?>
                        <tr>
                            <td class="order-id">#<?php echo $row['id']; ?></td>
                            <td class="table-num">Table <?php echo $row['table_number']; ?></td>
                            <td class="item-name"><?php echo $row['item_name']; ?></td>
                            <td class="price">Ksh <?php echo $row['price']; ?></td>
                            <td class="status-cell"><span class="status <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: { labels: <?php echo json_encode($days); ?>, datasets: [{ label: 'Revenue', data: <?php echo json_encode($sales); ?>, backgroundColor: '#ff6b35', borderRadius: 6 }] },
    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => 'Ksh ' + v.toLocaleString() } } } }
});
</script>

</body>
</html>