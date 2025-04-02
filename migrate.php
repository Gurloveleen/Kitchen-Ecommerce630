<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Select all users who still have plaintext passwords
$sql = "SELECT user_id, passcode FROM users WHERE salt IS NULL OR salt = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_id = $row["user_id"];
        $old_password = $row["passcode"]; // This is the plaintext password

        // Generate a random salt
        $salt = bin2hex(random_bytes(16));

        // Hash the old plaintext password with the new salt using MD5
        $new_hashed_password = md5($salt . $old_password);

        // Update the user record with the new hash and salt
        $update_sql = $conn->prepare("UPDATE users SET passcode = ?, salt = ? WHERE user_id = ?");
        $update_sql->bind_param("ssi", $new_hashed_password, $salt, $user_id);
        $update_sql->execute();
    }
    echo "Password migration successful!";
} else {
    echo "No passwords needed migration.";
}

$conn->close();
?>
