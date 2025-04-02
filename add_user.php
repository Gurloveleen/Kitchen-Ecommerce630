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

// Ensure only admins can add users
if (!isset($_SESSION['user']) || $_SESSION['user']['admin'] != 1) {
    die("Unauthorized access.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $tel = $_POST['tel'];
    $address = $_POST['address'];
    $city_code = $_POST['city_code'];
    $login_id = $_POST['login_id'];
    $password = $_POST['passcode'];
    $admin = isset($_POST['admin']) ? 1 : 0;

    // Generate a random salt
    $salt = bin2hex(random_bytes(8));
    $hashed_password = md5($salt . $password);

    $stmt = $conn->prepare("INSERT INTO Users (Name, Email, Tel, Address, City_Code, Login_Id, Passcode, Salt, Admin) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssi", $name, $email, $tel, $address, $city_code, $login_id, $hashed_password, $salt, $admin);
    
    if ($stmt->execute()) {
        header("Location: admin.php?message=User added successfully");
        exit();
    } else {
        die("Error adding user: " . $stmt->error);
    }

    $stmt->close();
}
$conn->close();
?>

<link rel="stylesheet" href="mainstyles.css">
<form method="post">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Phone: <input type="text" name="tel" required><br>
    Address: <input type="text" name="address" required><br>
    City Code: <input type="text" name="city_code" required><br>
    Login ID: <input type="text" name="login_id" required><br>
    Password: <input type="password" name="passcode" required><br>
    Admin: <input type="checkbox" name="admin"><br>
    <button type="submit">Add User</button>
</form>
