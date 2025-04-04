<?php
session_start();
include 'ecommerce_db.php';

if (!isset($_GET['order_id'])) {
    header("Location: error.php?msg=Invalid Order ID");
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$query = $conn->prepare("SELECT o.Order_Id, o.Total_Price, o.Payment_Code, o.Payment_Method, t.Source, t.Destination, t.Price as Trip_Price
                         FROM Orders o
                         JOIN Trips t ON o.Trip_Id = t.Trip_Id
                         WHERE o.Order_Id = ?");
$query->bind_param("i", $order_id);
$query->execute();
$query->bind_result($order_id, $total_price, $payment_code, $payment_method, $source, $destination, $trip_price);
$query->fetch();
$query->close();
$conn->close();


if (!$order_id) {
    header("Location: error.php?msg=Order not found");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <br>
    <br>
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
<header>
    <nav>
        <div class="left-item">
            <img src="images/logo3.png" alt="Behind The Counter" width="250" height="150">
        </div>
        <div class="center-items">
            <a href="page.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="shopping.php">Shopping</a>
            <a href="delivery.php">Delivery</a>
            <a href="cart.php">
                <div class="icon-cart">
                    <span id="cart-count">0</span>
                </div>
            </a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="logout.php">Log Out</a>
            <?php else: ?>
                <a href="signup.html">Sign Up</a>
                <a href="signin.html">Sign In</a>
            <?php endif; ?>
        </div>
    </nav>
    <h1>Order Confirmation</h1>
</header>

<main>
    <section class="confirmation-section">
        <h2>Thank You for Your Purchase!</h2>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_id); ?></p>
        <p><strong>Payment Code:</strong> <?php echo htmlspecialchars($payment_code); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(ucfirst($payment_method)); ?></p>
        <p><strong>Grand Total:</strong> $<?php echo number_format($total_price, 2); ?></p>
        <h3>Trip Details</h3>
        <p><strong>Source:</strong> <?php echo htmlspecialchars($source); ?></p>
        <p><strong>Destination:</strong> <?php echo htmlspecialchars($destination); ?></p>
        <p><strong>Trip Price:</strong> $<?php echo number_format($trip_price, 2); ?></p>
        <a href="page.php" class="btn">Return to Home</a>
    </section>
</main>
</body>
</html>