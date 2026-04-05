<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Test</h1>";

// Check if db_connect.php exists
if(!file_exists("db_connect.php")) {
    die("❌ db_connect.php file not found!");
}

include("db_connect.php");

if(!$conn) {
    die("❌ Database connection failed!");
}

echo "✅ Database connected successfully!<br><br>";

// Check all required tables
$required_tables = ['staff', 'menu', 'orders', 'customers', 'inventory', 'restaurant_tables'];
echo "<h3>Checking Tables:</h3>";

foreach($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if($result && $result->num_rows > 0) {
        $count = $conn->query("SELECT COUNT(*) as total FROM $table");
        $total = $count ? $count->fetch_assoc()['total'] : 0;
        echo "✅ $table table exists - $total rows<br>";
    } else {
        echo "❌ $table table MISSING!<br>";
    }
}

// Check staff data
echo "<h3>Staff Users:</h3>";
$staff = $conn->query("SELECT * FROM staff");
if($staff && $staff->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
    while($row = $staff->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>{$row['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ No staff users found! You need to add staff data.<br>";
}

$conn->close();
?>