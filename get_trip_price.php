<?php
session_start();
include 'ecommerce_db.php';

$trip_id = 1; // Modify this to be dynamic if needed
$trip_price = 0;

$query = $conn->prepare("SELECT Price FROM Trips WHERE Trip_Id = ?");
$query->bind_param("i", $trip_id);
$query->execute();
$query->bind_result($trip_price);
$query->fetch();
$query->close();
$conn->close();

echo json_encode(["trip_price" => $trip_price]);
?>
