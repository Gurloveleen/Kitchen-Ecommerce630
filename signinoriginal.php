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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["passcode"];  // User's entered password

    // Fetch user details including hashed password and salt
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Get stored hash and salt from database
        $stored_hash = $user["passcode"]; 
        $salt = $user["salt"]; 

        // Hash the entered password using the stored salt
        $hashed_input_password = md5($salt . $password);  

        // Compare the stored hash with the computed hash
        if ($hashed_input_password === $stored_hash) {
            $_SESSION["user"] = [
                "name" => $user["name"],
                "email" => $user["email"],
                "tel" => $user["tel"],
                "address" => $user["address"],
                "city_code" => $user["city_code"],
                "login_id" => $user["login_id"],
                "admin" => $user["admin"]
            ];

            header("Location: shopping.php");
            exit();
        } else {
            header("Location: signin.html?error=invalid");
            exit();
        }
    } else {
        header("Location: signin.html?error=invalid");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
