<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$user_name = $_SESSION['name'];

// Add staff
if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $pass = $_POST['pass'];
    $conn->query("INSERT INTO staff (name, email, role, password) VALUES ('$name', '$email', '$role', '$pass')");
    $success = "Staff added";
}

// Delete staff
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM staff WHERE id = '$id'");
        $success = "Deleted";
    }
}

$staff = $conn->query("SELECT * FROM staff ORDER BY id");
$total = $conn->query("SELECT COUNT(*) as c FROM staff")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/staff.css">
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
            <a href="staff.php" class="nav-item active"><i class="fas fa-users"></i><span>Staff</span></a>
            <a href="customers.php" class="nav-item"><i class="fas fa-user-friends"></i><span>Customers</span></a>
            <a href="inventory.php" class="nav-item"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="reports.php" class="nav-item"><i class="fas fa-chart-bar"></i><span>Reports</span></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small>Admin</small></div></div>
    </aside>

    <main class="main-content">
        <div class="top-bar"><div><h1>Staff Management</h1><p>Manage restaurant staff</p></div><div class="date"><?php echo date('F j, Y'); ?></div></div>

        <?php if(isset($success)): ?>
        <div class="alert"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="staff-stats"><div class="stat"><i class="fas fa-users"></i><div><h3><?php echo $total; ?></h3><p>Total Staff</p></div></div></div>

        <div class="add-form">
            <h3><i class="fas fa-user-plus"></i> Add Staff</h3>
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <select name="role"><option>Admin</option><option>Waiter</option><option>Kitchen</option></select>
                <input type="password" name="pass" placeholder="Password" required>
                <button type="submit" name="add">Add Staff</button>
            </form>
        </div>

        <div class="staff-table">
            <h3><i class="fas fa-list"></i> Staff List</h3>
            <div class="table-wrap">
                <table class="staff-list-table">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while($row = $staff->fetch_assoc()): ?>
                        <tr>
                            <td class="staff-id">#<?php echo $row['id']; ?></td>
                            <td class="staff-name"><i class="fas fa-user-circle"></i> <?php echo $row['name']; ?></td>
                            <td class="staff-email"><?php echo $row['email']; ?></td>
                            <td class="staff-role"><span class="role <?php echo strtolower($row['role']); ?>"><?php echo $row['role']; ?></span></td>
                            <td class="staff-action">
                                <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Delete?')">Delete</a>
                                <?php else: ?>
                                <span class="current">Current User</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="../js/staff.js"></script>
</body>
</html>