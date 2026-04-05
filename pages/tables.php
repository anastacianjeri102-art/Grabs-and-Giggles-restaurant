<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Allow Waiters and Admin
if($user_role != 'Waiter' && $user_role != 'Admin') {
    header("Location: orders.php");
    exit();
}

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $conn->query("UPDATE restaurant_tables SET status = '$status' WHERE id = '$id'");
    header("Location: tables.php");
    exit();
}

$tables = $conn->query("SELECT * FROM restaurant_tables ORDER BY table_number");
$available = $conn->query("SELECT COUNT(*) as c FROM restaurant_tables WHERE status='available'")->fetch_assoc()['c'];
$occupied = $conn->query("SELECT COUNT(*) as c FROM restaurant_tables WHERE status='occupied'")->fetch_assoc()['c'];
$reserved = $conn->query("SELECT COUNT(*) as c FROM restaurant_tables WHERE status='reserved'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tables | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/tables.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <a href="orders.php" class="nav-item"><i class="fas fa-clipboard-list"></i><span>Orders</span><?php if($pending>0) echo "<span class='badge'>$pending</span>"; ?></a>
            <a href="menu.php" class="nav-item"><i class="fas fa-utensil-spoon"></i><span>Menu</span></a>
            <a href="tables.php" class="nav-item active"><i class="fas fa-chair"></i><span>Tables</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info-sidebar"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small><?php echo $user_role; ?></small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar"><div class="page-title"><h1>Table Management</h1><p>View and manage tables</p></div></div>

        <div class="table-stats">
            <div class="stat available"><i class="fas fa-check-circle"></i><div><h3><?php echo $available; ?></h3><p>Available</p></div></div>
            <div class="stat occupied"><i class="fas fa-chair"></i><div><h3><?php echo $occupied; ?></h3><p>Occupied</p></div></div>
            <div class="stat reserved"><i class="fas fa-calendar"></i><div><h3><?php echo $reserved; ?></h3><p>Reserved</p></div></div>
        </div>

        <div class="tables-grid">
            <?php while($t = $tables->fetch_assoc()): ?>
            <div class="table-card <?php echo $t['status']; ?>">
                <div class="table-number">Table <?php echo $t['table_number']; ?></div>
                <div class="table-capacity"><i class="fas fa-users"></i> <?php echo $t['capacity']; ?> seats</div>
                <div class="table-status status-<?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></div>
                <div class="table-actions">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <input type="hidden" name="status" value="occupied">
                        <button type="submit" name="update" class="btn occ" <?php if($t['status']=='occupied') echo 'disabled'; ?>>Occupy</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <input type="hidden" name="status" value="reserved">
                        <button type="submit" name="update" class="btn res" <?php if($t['status']=='reserved') echo 'disabled'; ?>>Reserve</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                        <input type="hidden" name="status" value="available">
                        <button type="submit" name="update" class="btn avail" <?php if($t['status']=='available') echo 'disabled'; ?>>Free</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
</div>

</body>
</html>