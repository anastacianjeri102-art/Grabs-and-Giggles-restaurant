<?php

include("../db_connect.php");

$id = $_GET['id'];

$sql = "DELETE FROM orders WHERE id='$id'";

if($conn->query($sql) === TRUE){

header("Location: orders.php");

}else{

echo "Error deleting order";

}

?>