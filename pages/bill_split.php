<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_name = $_SESSION['name'];

// Get table number from URL
$table_number = isset($_GET['table']) ? intval($_GET['table']) : 0;

if($table_number == 0) {
    header("Location: orders.php");
    exit();
}

// Get all orders for this table
$orders = $conn->query("SELECT * FROM orders WHERE table_number = '$table_number' AND status = 'pending'");
$total_amount = 0;
$order_items = [];
while($order = $orders->fetch_assoc()) {
    $total_amount += $order['price'];
    $order_items[] = $order;
}

// Handle bill split
if(isset($_POST['split_bill'])) {
    $num_people = intval($_POST['num_people']);
    $amount_per_person = $total_amount / $num_people;
    
    $_SESSION['split_bill_data'] = [
        'table_number' => $table_number,
        'total_amount' => $total_amount,
        'num_people' => $num_people,
        'amount_per_person' => $amount_per_person,
        'items' => $order_items
    ];
    
    header("Location: payment.php?split=1");
    exit();
}

$pending_count = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Split | Grabs & Giggles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/bill_split.css">
</head>
<body>

<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-utensils"></i>
                <span>Grabs & Giggles</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <?php if($user_role == 'Admin'): ?>
                <a href="dashboard.php" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            <?php endif; ?>
            
            <a href="orders.php" class="nav-item">
                <i class="fas fa-clipboard-list"></i>
                <span>Orders</span>
                <?php if($pending_count > 0): ?>
                    <span class="badge"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a href="bill_split.php?table=<?php echo $table_number; ?>" class="nav-item active">
                <i class="fas fa-calculator"></i>
                <span>Split Bill</span>
            </a>
            
            <a href="logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </nav>
        
        <div class="user-info-sidebar">
            <i class="fas fa-user-circle"></i>
            <div>
                <p><?php echo $user_name; ?></p>
                <small><?php echo $user_role; ?></small>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <h1>Split Bill - Table <?php echo $table_number; ?></h1>
                <p>Divide the bill among customers</p>
            </div>
        </div>

        <div class="split-container">
            <div class="split-card">
                <div class="split-icon">
                    <i class="fas fa-users"></i>
                </div>
                
                < class="bill-summary">
                    <h3>Order Summary</h3>
                    <div class="order-items">
                        <?php foreach($order_items as $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['item_name']); ?></span>
                            <span>Ksh <?php echo number_format($item['price'], 0); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="total-amount">
                        <strong>Total Amount</strong>
                        <strong class="total-price">Ksh <?php echo number_format($total_amount, 0); ?></strong>
                    </div>
                
<input type="hidden" id="totalAmount" value="<?php echo $total_amount; ?>">
                
                <form method="POST" id="splitForm">
                    <div class="form-group-split">
                        <label><i class="fas fa-user-friends"></i> Number of People</label>
                        <input type="number" name="num_people" id="numPeople" class="people-input" 
                               min="1" max="20" value="2" required>
                    </div>
                    
                    <div class="per-person-card" id="perPersonCard">
                        <p>Each person pays:</p>
                        <h3 id="perPersonAmount">Ksh <?php echo number_format($total_amount / 2, 0); ?></h3>
                    </div>
                    
                    <button type="submit" name="split_bill" class="btn-split">
                        <i class="fas fa-calculator"></i> Split Bill & Continue to Payment
                    </button>
                </form>
                
                <button onclick="window.location.href='orders.php'" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </button>
            </div>
        </div>
    </main>
</div>

<script src="../js/bill_split.js"></script>
</body>
</html>