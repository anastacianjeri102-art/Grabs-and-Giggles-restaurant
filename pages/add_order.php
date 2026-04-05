<?php
session_start();
include("../db_connect.php");

if(isset($_POST['add_order'])) {
    $table = mysqli_real_escape_string($conn, $_POST['table_number']);
    $item = mysqli_real_escape_string($conn, $_POST['item_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Handle customer
    $customer_id = 'NULL';
    if(isset($_POST['customer_id'])) {
        if($_POST['customer_id'] == 'new' && !empty($_POST['new_name'])) {
            $new_name = mysqli_real_escape_string($conn, $_POST['new_name']);
            $new_phone = mysqli_real_escape_string($conn, $_POST['new_phone']);
            $conn->query("INSERT INTO customers (name, phone) VALUES ('$new_name', '$new_phone')");
            $customer_id = $conn->insert_id;
        } elseif($_POST['customer_id'] != '' && $_POST['customer_id'] != 'new') {
            $customer_id = $_POST['customer_id'];
        }
    }
    
    // Insert order
    $sql = "INSERT INTO orders (table_number, item_name, price, status, customer_id) 
            VALUES ('$table', '$item', '$price', 'pending', $customer_id)";
    
    if($conn->query($sql)) {
        // Update customer stats
        if($customer_id != 'NULL') {
            $conn->query("UPDATE customers SET total_orders = total_orders + 1, total_spent = total_spent + $price WHERE id = $customer_id");
        }
        
        // UPDATE TABLE STATUS TO OCCUPIED
        $conn->query("UPDATE restaurant_tables SET status = 'occupied' WHERE table_number = '$table'");
        
        header("Location: orders.php?success=Order added for Table $table");
        exit();
    } else {
        header("Location: menu.php?error=Failed to add order");
        exit();
    }
} else {
    header("Location: menu.php");
    exit();
}
?>