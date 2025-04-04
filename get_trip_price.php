<?php
include 'ecommerce_db.php';

// Set JSON header to avoid conflicts
header('Content-Type: application/json');

// Fetch the latest trip price
$trip_query = $conn->query("SELECT Trip_Id, Price FROM Trips ORDER BY Trip_Id DESC LIMIT 1");

if ($trip_query->num_rows > 0) {
    $trip = $trip_query->fetch_assoc();
    echo json_encode(["trip_price" => $trip['Price'], "trip_id" => $trip['Trip_Id']]);
} else {
    echo json_encode(["trip_price" => 0, "trip_id" => null]);
}

$conn->close();
?>
