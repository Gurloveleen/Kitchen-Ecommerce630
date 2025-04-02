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

// Ensure only admins can add items
if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $made_in = $_POST['made_in'];
    $department_code = $_POST['department_code'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO Items (Item_Name, Price, Made_In, Department_Code, Image_URL) 
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $name, $price, $made_in, $department_code, $image_url);

    if ($stmt->execute()) {
        header("Location: admin.php?message=Item added successfully");
        exit();
    } else {
        die("Error adding item: " . $stmt->error);
    }

    $stmt->close();
}
$conn->close();
?>
<link rel="stylesheet" href="mainstyles.css">
<form method="post">
    Item Name: <input type="text" name="name" required><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    Made In: <input type="text" name="made_in" required><br>
    Department Code: <input type="text" name="department_code" required><br>
    Image URL: <input type="text" name="image_url" required><br>
    <button type="submit">Add Item</button>
</form>
