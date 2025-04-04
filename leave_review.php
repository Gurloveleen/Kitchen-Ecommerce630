<?php
session_start();
if (!isset($_SESSION['user'])) {
    die("You must be logged in to leave a review.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure item_id is passed in the URL
if (!isset($_GET['item_id']) || !is_numeric($_GET['item_id'])) {
    die("Invalid item ID.");
}

$item_id = (int)$_GET['item_id'];
$user_id = (int)$_SESSION['user']; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = isset($_POST["rating"]) ? (int)$_POST["rating"] : 0;
    $review_text = isset($_POST["review"]) ? $conn->real_escape_string($_POST["review"]) : '';

    if ($rating < 1 || $rating > 5) {
        die("Invalid rating.");
    }

    // Ensure the user exists before inserting review
    $sql_check_user = "SELECT * FROM users WHERE User_Id = $user_id";
    $result_user = $conn->query($sql_check_user);

    if ($result_user->num_rows === 0) {
        die("User does not exist in database.");
    }

    // Insert review
    $sql = "INSERT INTO reviews (User_Id, Item_Id, Rating, Review_Text) VALUES ($user_id, $item_id, $rating, '$review_text')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: shopping.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review</title>
    <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
    <h2><center>Leave a Review</center></h2>
    <form method="post">
        <label for="rating">Rating (1-5):</label>
        <select name="rating" id="rating" required>
            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
        </select>
        <br><br>
        <label for="review">Your Review:</label><br>
        <textarea name="review" id="review" rows="4" cols="50" required></textarea>
        <br><br>
        <button type="submit">Submit Review</button>
    </form>
</body>
</html>
