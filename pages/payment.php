<?php
session_start();
include("../db_connect.php");

if(!isset($_SESSION['payment'])) {
    header("Location: orders.php");
    exit();
}

$p = $_SESSION['payment'];
$table = $p['table'];
$total = $p['total'];
$items = $p['items'];

// Group items
$grouped = [];
foreach($items as $item) {
    $name = $item['item_name'];
    if(isset($grouped[$name])) {
        $grouped[$name]['qty']++;
        $grouped[$name]['total'] += $item['price'];
    } else {
        $grouped[$name] = ['qty' => 1, 'total' => $item['price']];
    }
}

if(isset($_POST['pay'])) {
    $method = $_POST['method'];
    
    // Update orders
    $conn->query("UPDATE orders SET status = 'completed' WHERE table_number = '$table' AND status = 'pending'");
    
    // UPDATE TABLE STATUS TO AVAILABLE
    $conn->query("UPDATE restaurant_tables SET status = 'available' WHERE table_number = '$table'");
    
    unset($_SESSION['payment']);
    header("Location: orders.php?success=Payment successful for Table $table");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment | Grabs & Giggles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Poppins',sans-serif;background:#f5f7fb;padding:20px;}
        .container{max-width:500px;margin:0 auto;background:white;border-radius:24px;padding:30px;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
        .header{text-align:center;border-bottom:2px dashed #e2e8f0;padding-bottom:15px;margin-bottom:20px;}
        .header i{font-size:40px;color:#ff8c42;}
        .header h2{margin:10px 0 5px;}
        .info{background:#f8fafc;padding:15px;border-radius:12px;margin-bottom:20px;display:flex;justify-content:space-between;}
        table{width:100%;border-collapse:collapse;margin-bottom:20px;}
        th,td{padding:8px 0;text-align:left;border-bottom:1px solid #f0f2f5;}
        .text-center{text-align:center;}
        .text-right{text-align:right;}
        .total-row td{border-top:2px solid #e2e8f0;padding-top:12px;font-weight:bold;}
        select, input{width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:12px;margin-bottom:15px;font-family:inherit;}
        button{width:100%;background:#27ae60;color:white;border:none;padding:14px;border-radius:12px;font-weight:600;cursor:pointer;}
        button:hover{background:#219a52;}
        .cancel{background:#e2e8f0;color:#475569;margin-top:10px;}
        @media(max-width:600px){.container{padding:20px;}}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <i class="fas fa-receipt"></i>
        <h2>Grabs & Giggles</h2>
        <p>Restaurant Receipt</p>
    </div>

    <div class="info">
        <span>Table: <strong><?php echo $table; ?></strong></span>
        <span>Total: <strong>Ksh <?php echo number_format($total,0); ?></strong></span>
    </div>

      <table>
        <thead>
            <th>Item</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Price</th>
        </thead>
        <tbody>
            <?php foreach($grouped as $name => $d): ?>
             <tr>
                <td><?php echo htmlspecialchars($name); ?></td>
                <td class="text-center"><?php echo $d['qty']; ?></td>
                <td class="text-right">Ksh <?php echo $d['total']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>Ksh <?php echo number_format($total,0); ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <form method="POST">
        <label>Payment Method</label>
        <select name="method" id="methodSelect" required>
            <option value="Cash">💵 Cash</option>
            <option value="M-Pesa">📱 M-Pesa</option>
            <option value="Card">💳 Card</option>
        </select>
        
        <div id="mpesaField" style="display:none;">
            <input type="tel" name="mpesa" placeholder="M-Pesa Number (e.g., 0712345678)">
        </div>
        
        <button type="submit" name="pay">Confirm Payment</button>
    </form>
    
    <button class="cancel" onclick="window.location.href='orders.php'">Cancel</button>
</div>

<script>
    document.getElementById('methodSelect').addEventListener('change', function() {
        var mpesaDiv = document.getElementById('mpesaField');
        mpesaDiv.style.display = this.value === 'M-Pesa' ? 'block' : 'none';
    });
</script>
</body>
</html>