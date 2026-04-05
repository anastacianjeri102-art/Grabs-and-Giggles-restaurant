<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Get menu items
$menu = $conn->query("SELECT * FROM menu ORDER BY category, name");

// Get customers for dropdown
$customers = $conn->query("SELECT id, name, phone FROM customers ORDER BY name");

// Get pending count
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Menu | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <?php if($user_role == 'Admin'): ?>
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
            <?php endif; ?>
            <a href="orders.php" class="nav-item"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending>0) echo "<span class='badge'>$pending</span>"; ?></a>
            <a href="menu.php" class="nav-item active"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
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
        <div class="top-bar"><div><h1>Restaurant Menu</h1><p>Select items to order</p></div><div class="date"><?php echo date('F j, Y'); ?></div></div>

        <div class="menu-grid">
            <?php if($menu->num_rows > 0): ?>
                <?php while($item = $menu->fetch_assoc()): ?>
                <div class="menu-card">
                    <img src="../images/<?php echo $item['image'] ?? 'food.jpg'; ?>" class="menu-image" onerror="this.src='https://via.placeholder.com/300x160?text=Food'">
                    <div class="menu-content">
                        <span class="menu-category"><i class="fas fa-tag"></i> <?php echo $item['category']; ?></span>
                        <h3><?php echo $item['name']; ?></h3>
                        <div class="menu-price">Ksh <?php echo number_format($item['price'], 0); ?></div>
                        
                        <?php if($user_role == 'Waiter' || $user_role == 'Admin'): ?>
                        <form action="add_order.php" method="POST" class="order-form">
                            <input type="number" name="table_number" placeholder="Table Number" required>
                            
                            <select name="customer_id" class="customer-select" onchange="toggleNewCustomer(this)">
                                <option value="">-- Select Customer (Optional) --</option>
                                <?php 
                                $customers->data_seek(0);
                                while($c = $customers->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?> (<?php echo $c['phone']; ?>)</option>
                                <?php endwhile; ?>
                                <option value="new">+ Add New Customer</option>
                            </select>
                            
                            <div class="new-customer-form">
                                <input type="text" name="new_name" placeholder="Customer Name">
                                <input type="tel" name="new_phone" placeholder="Phone Number">
                                <input type="email" name="new_email" placeholder="Email (Optional)">
                            </div>
                            
                            <input type="hidden" name="item_name" value="<?php echo $item['name']; ?>">
                            <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                            <button type="submit" name="add_order" class="add-order-btn">Add to Order</button>
                        </form>
                        <?php else: ?>
                        <div class="kitchen-note"><i class="fas fa-lock"></i> Kitchen staff cannot place orders</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-menu">No menu items found. Please add items in admin panel.</div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="../js/menu.js"></script>
</body>
</html>