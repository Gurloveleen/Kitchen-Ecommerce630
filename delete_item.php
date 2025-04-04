<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user session data
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    die("Unauthorized access.");
}

// Validate item_id from GET request
if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    die("Invalid item ID.");
}

$item_id = intval($_GET['item_id']);

// Delete item from database
$stmt = $conn->prepare("DELETE FROM Items WHERE Item_Id = ?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    header("Location: admin.php?message=Item deleted successfully");
    exit();
} else {
    die("Deletion failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
