<?php
$servername = "sql306.infinityfree.com";
$username = "if0_41508824";
$password = "Fl8Zm7Oqw2mDcw8";
$dbname = "if0_41508824_restaurant_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
