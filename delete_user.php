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

// Ensure only admins can access this page
if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
    die("Unauthorized access.");
}

// Validate user_id from GET request
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['user_id']);

// Prevent admin from deleting themselves
if ($_SESSION['user']['login_id'] == $user_id) {
    die("You cannot delete your own account.");
}

// Fetch user details to ensure the user exists
$stmt = $conn->prepare("SELECT user_id FROM Users WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}
$stmt->close();

// Proceed with deletion
$stmt = $conn->prepare("DELETE FROM Users WHERE user_id=?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: admin.php?message=User deleted successfully");
    exit();
} else {
    die("Deletion failed: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
