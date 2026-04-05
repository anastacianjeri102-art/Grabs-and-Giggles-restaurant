<?php
session_start();
include("../db_connect.php");

if(isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $conn->query("UPDATE orders SET status = '$status' WHERE id = '$id'");
}
header("Location: orders.php");
?>