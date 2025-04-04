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

// Fetch item details
$stmt = $conn->prepare("SELECT * FROM Items WHERE Item_Id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Item not found.");
}

$item = $result->fetch_assoc();
$stmt->close();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $made_in = $_POST['made_in'];
    $department_code = $_POST['department_code'];

    // Update item details
    $stmt = $conn->prepare("UPDATE Items SET Item_Name=?, Price=?, Made_In=?, Department_Code=? WHERE Item_Id=?");
    $stmt->bind_param("sdssi", $name, $price, $made_in, $department_code, $item_id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=Item updated successfully");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title> 
    <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
    <h2><center>Edit Item</center></h2>
    <form method="post">
        <label>Item Name: <input type="text" name="name" value="<?= htmlspecialchars($item['Item_Name']) ?>" required></label><br>
        <label>Price: <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($item['Price']) ?>" required></label><br>
        <label>Made In: <input type="text" name="made_in" value="<?= htmlspecialchars($item['Made_In']) ?>" required></label><br>
        <label>Department Code: <input type="text" name="department_code" value="<?= htmlspecialchars($item['Department_Code']) ?>" required></label><br>
        <button type="submit">Update Item</button>
    </form>
    <a href="admin.php"><center>Back to Admin Panel</center></a>
</body>
</html>
