<?php
include("../db_connect.php");

echo "<h2>Orders Table Diagnostic</h2>";

// Check if orders table exists
$table_check = $conn->query("SHOW TABLES LIKE 'orders'");
if($table_check->num_rows > 0) {
    echo "✅ Orders table exists<br><br>";
    
    // Check table structure
    $structure = $conn->query("DESCRIBE orders");
    echo "<h3>Orders Table Structure:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    while($col = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // Check data
    $count = $conn->query("SELECT COUNT(*) as total FROM orders");
    $total = $count->fetch_assoc()['total'];
    echo "📊 Total orders in table: $total<br><br>";
    
    // Show sample data
    if($total > 0) {
        $sample = $conn->query("SELECT * FROM orders LIMIT 3");
        echo "<h3>Sample Orders:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Table</th><th>Item</th><th>Price</th><th>Status</th></tr>";
        while($row = $sample->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['table_number']}</td>";
            echo "<td>{$row['item_name']}</td>";
            echo "<td>{$row['price']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No data in orders table. Add some test orders.<br>";
    }
    
} else {
    echo "❌ Orders table does NOT exist!<br>";
    echo "Run this SQL to create it:<br>";
    echo "<pre>
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
    </pre>";
}

$conn->close();
?>