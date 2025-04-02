<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$source = $_POST['source'];
$destination = $_POST['destination'];
$distance = $_POST['distance'];
$price = $_POST['price'];

$sql = "INSERT INTO Trips (Source, Destination, Distance_KM, Price) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdd", $source, $destination, $distance, $price);

if ($stmt->execute()) {
    echo "Trip saved successfully!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
