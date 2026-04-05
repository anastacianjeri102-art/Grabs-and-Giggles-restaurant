<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Handle delete order
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id = '$id'");
    header("Location: orders.php?success=Order deleted");
    exit();
}

// Handle complete order
if(isset($_GET['complete'])) {
    $id = $_GET['complete'];
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = '$id'");
    header("Location: orders.php?success=Order completed");
    exit();
}

// Process payment - redirect to payment page
if(isset($_POST['pay_table'])) {
    $table_number = $_POST['table_number'];
    $orders = $conn->query("SELECT * FROM orders WHERE table_number = '$table_number' AND status = 'pending'");
    $total = 0;
    $items = [];
    while($o = $orders->fetch_assoc()) {
        $total += $o['price'];
        $items[] = $o;
    }
    $_SESSION['payment'] = ['table' => $table_number, 'total' => $total, 'items' => $items];
    header("Location: payment.php");
    exit();
}

// Split bill
if(isset($_POST['split_bill'])) {
    $table_number = $_POST['table_number'];
    $people = $_POST['people'];
    $orders = $conn->query("SELECT * FROM orders WHERE table_number = '$table_number' AND status = 'pending'");
    $total = 0;
    $items = [];
    while($o = $orders->fetch_assoc()) {
        $total += $o['price'];
        $items[] = $o;
    }
    $per_person = $total / $people;
    $_SESSION['payment'] = ['table' => $table_number, 'total' => $total, 'per_person' => $per_person, 'people' => $people, 'items' => $items, 'split' => true];
    header("Location: payment.php");
    exit();
}

// Get all orders
$orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");

// Get statistics
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$completed = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='completed'")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT SUM(price) as total FROM orders WHERE status='completed'")->fetch_assoc()['total'];

// Get tables with pending orders
$tables_with_orders = $conn->query("SELECT DISTINCT table_number FROM orders WHERE status='pending'");

$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        /* Orders Page Styles */
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-mini-card {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .stat-mini-card i {
            font-size: 2rem;
        }
        .stat-mini-card h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }
        .stat-mini-card p {
            font-size: 0.7rem;
            color: #7f8c8d;
            margin: 0;
        }
        .pending-stat i, .pending-stat h3 { color: #f39c12; }
        .completed-stat i, .completed-stat h3 { color: #27ae60; }
        .revenue-stat i, .revenue-stat h3 { color: #ff6b35; }
        
        .table-actions-card {
            background: white;
            border-radius: 16px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .table-actions-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
        }
        .table-action {
            background: #f8fafc;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid #e2e8f0;
        }
        .table-action strong {
            font-size: 0.9rem;
        }
        .btn-pay, .btn-split {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.7rem;
        }
        .btn-pay {
            background: #27ae60;
            color: white;
        }
        .btn-split {
            background: #3498db;
            color: white;
        }
        .btn-split input {
            width: 50px;
            padding: 0.3rem;
            margin-right: 0.3rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }
        
        .recent-orders-card {
            background: white;
            border-radius: 16px;
            padding: 1rem;
        }
        .card-header {
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f0f2f5;
        }
        .card-header h3 {
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        .orders-table th {
            text-align: left;
            padding: 0.8rem 0.5rem;
            background: #f8fafc;
            font-weight: 600;
            font-size: 0.7rem;
            color: #2c3e50;
            border-bottom: 2px solid #eef2f6;
        }
        .orders-table td {
            padding: 0.8rem 0.5rem;
            border-bottom: 1px solid #f0f2f5;
            font-size: 0.75rem;
            vertical-align: middle;
        }
        .order-id {
            font-weight: 700;
            color: #ff6b35;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-pending {
            background: #fff3e0;
            color: #ed6c02;
        }
        .action-btn {
            padding: 4px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
            margin: 0 2px;
        }
        .complete-btn {
            background: #27ae60;
            color: white;
        }
        .delete-btn {
            background: #e74c3c;
            color: white;
        }
        .completed-label {
            color: #27ae60;
            font-size: 0.65rem;
            background: #e8f5e9;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-block;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 0.8rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 3px solid #27ae60;
        }
        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #94a3b8;
        }
        @media (max-width: 768px) {
            .stats-mini { grid-template-columns: 1fr; }
            .table-actions-grid { flex-direction: column; }
            .table-action { flex-wrap: wrap; justify-content: center; }
            .orders-table th, .orders-table td { padding: 0.5rem 0.3rem; font-size: 0.65rem; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <?php if($user_role == 'Admin'): ?>
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <?php endif; ?>
            <a href="orders.php" class="nav-item active"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending>0) echo "<span class='badge'>$pending</span>"; ?></a>
            <a href="menu.php" class="nav-item"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
            <a href="tables.php" class="nav-item"><i class="fas fa-chair"></i><span>Tables</span></a>
            <?php if($user_role == 'Admin'): ?>
            <a href="staff.php" class="nav-item"><i class="fas fa-users"></i><span>Staff</span></a>
            <a href="customers.php" class="nav-item"><i class="fas fa-user-friends"></i><span>Customers</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <?php endif; ?>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small><?php echo $user_role; ?></small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div><h1>Order Management</h1><p>Welcome, <?php echo $user_name; ?></p></div>
            <div class="date"><?php echo date('F j, Y'); ?></div>
        </div>

        <?php if($success): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-mini">
            <div class="stat-mini-card pending-stat"><i class="fas fa-clock"></i><div><h3><?php echo $pending; ?></h3><p>Pending Orders</p></div></div>
            <div class="stat-mini-card completed-stat"><i class="fas fa-check-circle"></i><div><h3><?php echo $completed; ?></h3><p>Completed Orders</p></div></div>
            <div class="stat-mini-card revenue-stat"><i class="fas fa-chart-line"></i><div><h3>Ksh <?php echo number_format($revenue); ?></h3><p>Total Revenue</p></div></div>
        </div>

        <!-- Table Actions for Pending Orders -->
        <?php if($tables_with_orders && $tables_with_orders->num_rows > 0): ?>
        <div class="table-actions-card">
            <h3><i class="fas fa-chair"></i> Table Actions</h3>
            <div class="table-actions-grid">
                <?php while($t = $tables_with_orders->fetch_assoc()): ?>
                <div class="table-action">
                    <strong>Table <?php echo $t['table_number']; ?></strong>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="table_number" value="<?php echo $t['table_number']; ?>">
                        <button type="submit" name="pay_table" class="btn-pay">Pay Bill</button>
                    </form>
                    <form method="POST" style="display:inline-flex; gap:5px; align-items:center;">
                        <input type="hidden" name="table_number" value="<?php echo $t['table_number']; ?>">
                        <input type="number" name="people" placeholder="People" style="width:60px;" required>
                        <button type="submit" name="split_bill" class="btn-split">Split Bill</button>
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Orders Table -->
        <div class="recent-orders-card">
            <div class="card-header"><h3><i class="fas fa-list"></i> Orders List</h3></div>
            <div class="table-responsive">
                <table class="orders-table">
                    <thead>
                        30
                            <th>Order ID</th>
                            <th>Table</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </thead>
                    <tbody>
                        <?php if($orders && $orders->num_rows > 0): ?>
                            <?php while($order = $orders->fetch_assoc()): ?>
                            <tr data-status="<?php echo $order['status']; ?>">
                                <td class="order-id">#<?php echo $order['id']; ?> </span>
                                <td class="table-num">Table <?php echo $order['table_number']; ?> </span>
                                <td class="item-name"><?php echo $order['item_name']; ?> </span>
                                <td class="price">Ksh <?php echo $order['price']; ?> </span>
                                <td class="status-cell">
                                    <span class="status-badge <?php echo $order['status'] == 'completed' ? 'status-completed' : 'status-pending'; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </span>
                                <td class="actions-cell">
                                    <?php if($order['status'] == 'pending'): ?>
                                        <a href="?complete=<?php echo $order['id']; ?>" class="action-btn complete-btn" onclick="return confirm('Complete this order?')">Complete</a>
                                        <a href="?delete=<?php echo $order['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Delete this order?')">Delete</a>
                                    <?php else: ?>
                                        <span class="completed-label">Completed</span>
                                    <?php endif; ?>
                                </span>
                            </span>
                            <?php endwhile; ?>
                        <?php else: ?>
                            30<td colspan="6" class="empty-state">No orders found. <a href="menu.php">Add orders from menu</a></span> </span>
                        <?php endif; ?>
                    </tbody>
                </span>
            </div>
        </div>
    </main>
</div>

</body>
</html>