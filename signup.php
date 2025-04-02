<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["name"];
    $email = $_POST["email"];
    $tel = $_POST["tel"];
    $address = $_POST["address"];
    $city_code = $_POST["city_code"];
    $login_id = $_POST["login_id"];
    $password = $_POST["passcode"];  

    // Generate a random salt (16 characters)
    $salt = bin2hex(random_bytes(8));

    // Hash the password with salt
    $hashed_password = md5($salt . $password); 

    // Insert into database
    $sql = "INSERT INTO users (name, email, tel, address, city_code, login_id, passcode, salt) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $full_name, $email, $tel, $address, $city_code, $login_id, $hashed_password, $salt);

    if ($stmt->execute()) {
        header("Location: signin.html?signup=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
