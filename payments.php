<?php
session_start();
include 'ecommerce_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user'])) {
        header("Location: error.php?msg=User not logged in");
        exit();
    }
    
    $user_id = $_SESSION['user'];
    $total_price = floatval($_POST['total_price']); 
    $payment_code = uniqid("PAY-");
    $payment_method = $_POST['payment_method'];  // Get selected payment method
    
    // Fetch trip price from database
    $trip_query = $conn->query("SELECT Trip_Id, Price FROM Trips ORDER BY Trip_Id DESC LIMIT 1");
    $trip = $trip_query->fetch_assoc();
    $trip_id = $trip['Trip_Id'];
    $trip_price = $trip['Price'];

    $total_price += $trip_price;

    // Insert order with payment method
    $stmt = $conn->prepare("INSERT INTO Orders (Total_Price, Payment_Code, User_Id, Trip_Id, Payment_Method) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("dsiis", $total_price, $payment_code, $user_id, $trip_id, $payment_method);

    if ($stmt->execute()) {
        header("Location: confirmation.php?order_id=" . $conn->insert_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
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
    <h1>Payment</h1>
</header>

<main>
    <section class="payment-section">
        <div class="invoice-summary">
            <h3>Invoice Summary</h3>
            <table id="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="cart-items"><!-- Items loaded from JavaScript --></tbody>
            </table>

           
            <p><strong>Delivery Fee:</strong> <span id="delivery-fee">$0.00</span></p>
            
            <p><strong>Grand Total:</strong> <span id="grand-total">$0.00</span></p>
        </div>

        <div class="payment-form">
            <h3>Enter Your Payment Details</h3>
            <form id="payment-form" method="POST" action="payments.php">
                <input type="hidden" name="total_price" id="hidden-total-price">
                
                <!-- Alternate Payment Methods Dropdown -->
                <label for="payment-method">Payment Method:</label>
                <select id="payment-method" name="payment_method" required>
                    <option value="credit">Credit Card</option>
                    <option value="debit">Debit Card</option>
                    <option value="gift">Gift Card</option>
                </select>

                <label for="card-holder-name">Cardholder Name:</label>
                <input type="text" id="card-holder-name" name="card_holder_name" required>

                <label for="card-number">Card Number:</label>
                <input type="text" id="card-number" name="card_number" required>

                <label for="expiry-date">Expiry Date:</label>
                <input type="month" id="expiry-date" name="expiry_date" required>

                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>

                <button type="submit" class="submit-btn">Complete Payment</button>
            </form>
        </div>
    </section>
</main>

<script>
function loadCart() {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const cartContainer = document.getElementById("cart-items");
    let grandTotal = 0;

    if (cart.length === 0) {
        cartContainer.innerHTML = "<tr><td colspan='4'>Your cart is empty.</td></tr>";
        document.getElementById("delivery-fee").textContent = "$0.00";
        document.getElementById("grand-total").textContent = "$0.00";
        return;
    }

    // Calculate total from all cart items
    cartContainer.innerHTML = "";
    cart.forEach((item) => {
        const itemTotal = item.price * item.quantity;
        grandTotal += itemTotal;
        cartContainer.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>$${item.price}</td>
                <td>$${itemTotal.toFixed(2)}</td>
            </tr>
        `;
    });

    // Fetch the trip/delivery fee
    fetch("get_trip_price.php")
    .then(response => response.json())
    .then(data => {
        console.log("Trip Data Fetched:", data);

        const deliveryFee = parseFloat(data.trip_price) || 0;
        document.getElementById("delivery-fee").textContent = "$" + deliveryFee.toFixed(2);

        // Fetch the cart total from the previously calculated value
        let cartTotal = 0;
        document.querySelectorAll("#cart-table tbody tr").forEach(row => {
            const priceCell = row.children[3]; // Fourth column (total price per item)
            if (priceCell) {
                cartTotal += parseFloat(priceCell.textContent.replace("$", "")) || 0;
            }
        });

        // Calculate grand total correctly
        const grandTotal = cartTotal + deliveryFee;
        document.getElementById("grand-total").textContent = "$" + grandTotal.toFixed(2);
        document.getElementById("hidden-total-price").value = grandTotal.toFixed(2);
    })


}

window.onload = loadCart;
</script>
</body>
</html>