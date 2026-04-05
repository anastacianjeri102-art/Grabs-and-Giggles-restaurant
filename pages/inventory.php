<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$user_name = $_SESSION['name'];

// Add item
if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $unit = $_POST['unit'];
    $reorder = $_POST['reorder'];
    $price = $_POST['price'];
    $conn->query("INSERT INTO inventory (item_name, quantity, unit, reorder_level, price_per_unit) VALUES ('$name', '$qty', '$unit', '$reorder', '$price')");
    $success = "Item added";
}

// Update quantity
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    $conn->query("UPDATE inventory SET quantity = '$qty' WHERE id = '$id'");
    $success = "Updated";
}

// Delete
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM inventory WHERE id = '$id'");
    $success = "Deleted";
}

$items = $conn->query("SELECT * FROM inventory ORDER BY category, item_name");
$total = $conn->query("SELECT COUNT(*) as c FROM inventory")->fetch_assoc()['c'];
$low = $conn->query("SELECT COUNT(*) as c FROM inventory WHERE quantity <= reorder_level")->fetch_assoc()['c'];
$value = $conn->query("SELECT SUM(quantity * price_per_unit) as v FROM inventory")->fetch_assoc()['v'];
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/inventory.css">
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
            <a href="inventory.php" class="nav-item active"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small>Admin</small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar"><div><h1>Inventory</h1><p>Manage stock levels</p></div><div class="date"><?php echo date('F j, Y'); ?></div></div>

        <!-- Stats -->
        <div class="inv-stats">
            <div class="stat"><i class="fas fa-boxes"></i><div><h3><?php echo $total; ?></h3><p>Total Items</p></div></div>
            <div class="stat"><i class="fas fa-exclamation-triangle"></i><div><h3><?php echo $low; ?></h3><p>Low Stock</p></div></div>
            <div class="stat"><i class="fas fa-coins"></i><div><h3>Ksh <?php echo number_format($value); ?></h3><p>Total Value</p></div></div>
        </div>

        <!-- Add Form -->
        <div class="add-form">
            <h3><i class="fas fa-plus"></i> Add Item</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Item Name" required>
                <input type="number" name="qty" placeholder="Quantity" required>
                <select name="unit"><option>kg</option><option>g</option><option>liters</option><option>pieces</option><option>units</option></select>
                <input type="number" name="reorder" placeholder="Reorder Level" required>
                <input type="number" name="price" step="0.01" placeholder="Price/Unit" required>
                <button type="submit" name="add">Add Item</button>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="inv-table">
            <h3><i class="fas fa-list"></i> All Items</h3>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Item</th><th>Qty</th><th>Unit</th><th>Reorder</th><th>Price</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php while($row = $items->fetch_assoc()): ?>
                        <tr class="<?php echo $row['quantity'] <= $row['reorder_level'] ? 'low' : ''; ?>">
                            <td><?php echo $row['item_name']; ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:5px;">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                    <input type="number" name="qty" value="<?php echo $row['quantity']; ?>" style="width:70px;">
                                    <button type="submit" name="update">Save</button>
                                </form>
                            </td>
                            <td><?php echo $row['unit']; ?></td>
                            <td><?php echo $row['reorder_level']; ?></td>
                            <td>Ksh <?php echo $row['price_per_unit']; ?></td>
                            <td><span class="status <?php echo $row['quantity'] <= $row['reorder_level'] ? 'low' : 'ok'; ?>"><?php echo $row['quantity'] <= $row['reorder_level'] ? 'Low' : 'OK'; ?></span></td>
                            <td><a href="?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Delete?')">Delete</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>