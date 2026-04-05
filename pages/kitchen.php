<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Only Kitchen and Admin can access
if($user_role != 'Kitchen' && $user_role != 'Admin') {
    header("Location: orders.php");
    exit();
}

// Complete table
if(isset($_POST['complete'])) {
    $table = $_POST['table'];
    $conn->query("UPDATE orders SET status = 'completed' WHERE table_number = '$table' AND status = 'pending'");
    header("Location: kitchen.php");
    exit();
}

$pending = $conn->query("SELECT * FROM orders WHERE status = 'pending' ORDER BY table_number");
$orders_by_table = [];
while($o = $pending->fetch_assoc()) {
    $orders_by_table[$o['table_number']][] = $o;
}
$pending_count = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kitchen | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/kitchen.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo"><i class="fas fa-utensils"></i><span>Grabs & Giggles</span></div></div>
        <nav class="sidebar-nav">
            <a href="kitchen.php" class="nav-item active"><i class="fas fa-utensils"></i><span>Kitchen</span><?php if($pending_count>0) echo "<span class='badge'>$pending_count</span>"; ?></a>
            <a href="logout.php" class="nav-item logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
        <div class="user-info"><i class="fas fa-user-circle"></i><div><p><?php echo $user_name; ?></p><small><?php echo $user_role; ?></small></div></div>
    </aside>

    <main class="main-content">
        <div class="kitchen-header">
            <div><h1>Kitchen Display</h1><p>Real-time orders from waiters</p></div>
            <div class="auto-refresh"><i class="fas fa-sync-alt"></i> <span id="countdown">10</span>s</div>
        </div>

        <?php if($pending_count > 0): ?>
        <div class="kitchen-grid">
            <?php foreach($orders_by_table as $table => $items): ?>
            <div class="kitchen-card">
                <div class="kitchen-card-header">
                    <strong><i class="fas fa-chair"></i> Table <?php echo $table; ?></strong>
                    <span class="item-badge"><?php echo count($items); ?> item(s)</span>
                </div>
                <div class="kitchen-items">
                    <?php foreach($items as $item): ?>
                    <div class="kitchen-item">
                        <span><i class="fas fa-hamburger"></i> <?php echo $item['item_name']; ?></span>
                        <span class="price">Ksh <?php echo $item['price']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="table" value="<?php echo $table; ?>">
                    <button type="submit" name="complete" class="complete-btn">Complete Table <?php echo $table; ?></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-kitchen">
            <i class="fas fa-check-circle"></i>
            <h3>No Pending Orders</h3>
            <p>All orders completed. Great job!</p>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
let seconds = 10;
setInterval(() => {
    seconds--;
    document.getElementById('countdown').innerText = seconds;
    if(seconds <= 0) location.reload();
}, 1000);
</script>

</body>
</html>