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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = intval($_POST['user_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $admin = isset($_POST['admin']) ? 1 : 0;

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE Users SET Name=?, Email=?, Admin=? WHERE user_id=?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssii", $name, $email, $admin, $user_id);
    
    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }
}

// Validate GET request
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['user_id']);
$stmt = $conn->prepare("SELECT user_id, Name, Email, Admin FROM Users WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
    <h2>Edit User</h2>
    <form method="post">
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['Name']) ?>" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
        <br>
        <label>Admin:</label>
        <input type="checkbox" name="admin" <?= $user['Admin'] ? 'checked' : '' ?>>
        <br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
