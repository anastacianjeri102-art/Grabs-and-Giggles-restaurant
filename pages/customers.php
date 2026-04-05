<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Only Admin can access
if($user_role != 'Admin') {
    header("Location: orders.php");
    exit();
}

// Add customer
if(isset($_POST['add_customer'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    $check = $conn->query("SELECT id FROM customers WHERE phone = '$phone'");
    if($check && $check->num_rows > 0) {
        $error = "Customer with this phone already exists!";
    } else {
        $conn->query("INSERT INTO customers (name, phone) VALUES ('$name', '$phone')");
        $success = "Customer added successfully!";
    }
}

// Delete customer
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM customers WHERE id = '$id'");
    $success = "Customer deleted!";
}

// Update customer stats
$conn->query("UPDATE customers c 
    SET total_orders = (SELECT COUNT(*) FROM orders WHERE customer_id = c.id),
        total_spent = (SELECT COALESCE(SUM(price),0) FROM orders WHERE customer_id = c.id),
        item_ordered = (SELECT GROUP_CONCAT(DISTINCT item_name SEPARATOR ', ') FROM orders WHERE customer_id = c.id)");

// Get all customers (distinct by phone)
$customers = $conn->query("SELECT * FROM customers GROUP BY phone ORDER BY id ASC");

// Get statistics
$total_customers = $conn->query("SELECT COUNT(DISTINCT phone) as count FROM customers")->fetch_assoc()['count'] ?? 0;
$total_spent = $conn->query("SELECT SUM(total_spent) as total FROM customers")->fetch_assoc()['total'] ?? 0;
$avg_spent = $total_customers > 0 ? $total_spent / $total_customers : 0;
$top_customer = $conn->query("SELECT name, total_spent FROM customers ORDER BY total_spent DESC LIMIT 1")->fetch_assoc();

$pending_count = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/customers.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <a href="orders.php" class="nav-item"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending_count>0) echo "<span class='badge'>$pending_count</span>"; ?></a>
            <a href="menu.php" class="nav-item"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
            <a href="tables.php" class="nav-item"><i class="fas fa-chair"></i><span>Tables</span></a>
            <a href="staff.php" class="nav-item"><i class="fas fa-users"></i><span>Staff</span></a>
            <a href="customers.php" class="nav-item active"><i class="fas fa-user-friends"></i><span>Customers</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info-sidebar"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small><?php echo $user_role; ?></small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar"><div class="page-title"><h1>Customer Management</h1><p>Track customer history, orders, and spending</p></div></div>

        <?php if(isset($success)): ?>
        <div class="alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
        <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="customer-stats">
            <div class="stat-card"><i class="fas fa-users"></i><div><h3><?php echo $total_customers; ?></h3><p>Total Customers</p></div></div>
            <div class="stat-card"><i class="fas fa-coins"></i><div><h3>Ksh <?php echo number_format($total_spent); ?></h3><p>Total Spent</p></div></div>
            <div class="stat-card"><i class="fas fa-chart-line"></i><div><h3>Ksh <?php echo number_format($avg_spent, 2); ?></h3><p>Average Spend</p></div></div>
            <div class="stat-card"><i class="fas fa-trophy"></i><div><h3><?php echo $top_customer ? htmlspecialchars($top_customer['name']) : '—'; ?></h3><p>Top Customer</p></div></div>
        </div>

        <!-- Add Customer Form -->
        <div class="form-card">
            <h3><i class="fas fa-user-plus"></i> Add New Customer</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group"><input type="text" name="name" placeholder="Full Name" required></div>
                    <div class="form-group"><input type="tel" name="phone" placeholder="Phone Number" required></div>
                    <div class="form-group"><button type="submit" name="add_customer" class="btn-primary">Add Customer</button></div>
                </div>
            </form>
        </div>

        <!-- Customer List -->
        <div class="table-card">
            <div class="card-header">
                <h3><i class="fas fa-list"></i> All Customers</h3>
                <div class="search-box"><i class="fas fa-search"></i><input type="text" id="searchCustomer" placeholder="Search by name or phone..."></div>
            </div>
            <div class="table-responsive">
                <table class="customer-table">
                    <thead>
                        60
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Items Ordered</th>
                            <th>Orders</th>
                            <th>Total Spent</th>
                            <th>Action</th>
                        </thead>
                    <tbody>
                        <?php if($customers && $customers->num_rows > 0): 
                            $counter = 1;
                            while($c = $customers->fetch_assoc()): 
                        ?>
                        <tr>
                            <td class="customer-id"><?php echo $counter++; ?></td>
                            <td class="customer-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($c['name']); ?></td>
                            <td class="customer-phone"><?php echo $c['phone'] ?? '—'; ?></td>
                            <td class="customer-items"><?php echo $c['item_ordered'] ? htmlspecialchars($c['item_ordered']) : '—'; ?></td>
                            <td class="customer-orders"><?php echo $c['total_orders']; ?></td>
                            <td class="customer-spent">Ksh <?php echo number_format($c['total_spent'], 0); ?></td>
                            <td class="customer-actions"><a href="?delete=<?php echo $c['id']; ?>" class="delete-btn" onclick="return confirm('Delete this customer?')">Delete</a></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7" class="empty-state">No customers found</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="../js/customers.js"></script>
</body>
</html>