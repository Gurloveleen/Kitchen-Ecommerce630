<?php
session_start();

$admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : false;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT i.*, COALESCE(AVG(r.Rating), 0) AS Avg_Rating, COUNT(r.Review_Id) AS Total_Reviews
        FROM items i
        LEFT JOIN reviews r ON i.Item_Id = r.Item_Id
        WHERE (i.sale_end_time IS NULL OR i.sale_end_time > NOW()) 
        GROUP BY i.Item_Id";
$result = $conn->query($sql);



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping</title>
    <script defer src="script.js"></script>
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
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7H7.312"/>
                        </svg>
                        <span id="cart-count">0</span>
                    </div>
                </a>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if ($admin): ?>
                        <a href="admin.php">Admin Page</a>
                    <?php endif; ?>
                    <a href="logout.php">Log Out</a>
                <?php else: ?>
                    <a href="signup.html">Sign Up</a>
                    <a href="signin.html">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
        <br><br><br><br><h1>Product List</h1>
    </header>

    
    <main>
    <h1>Product List</h1>

    <div class="cart" id="cart" ondrop="drop(event)" ondragover="allowDrop(event)">
        <img src="images/cart.png" alt="Cart Icon" />
        <ul class="cart-items" id="cart-items"></ul>
    </div>

    <h2>Available Products</h2>
    <div class="listProduct">
    <?php                
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $is_on_sale = false; 
            $discount_price = null;

            
            if ($row["sale_price"] !== null && !empty($row["sale_end_time"]) && strtotime($row["sale_end_time"]) > time()) {
                $is_on_sale = true;
                $discount_price = $row["sale_price"];
            }

            echo '<div class="item" draggable="true" data-id="' . $row["Item_Id"] . '" data-name="' . $row["Item_Name"] . '" data-price="' . ($is_on_sale ? $discount_price : $row["Price"]) . '" data-image="' . $row["Image_URL"] . '" data-sale-end-time="' . $row["sale_end_time"] . '">';

            echo '<img src="' . $row["Image_URL"] . '" alt="' . $row["Item_Name"] . '" width="200" height="200">';
            echo '<h2>' . $row["Item_Name"] . '</h2>';

            if ($is_on_sale) {
                echo '<div class="price">Was $' . $row["Price"] . ' <strong>Now $' . $discount_price . '</strong></div>';
                echo '<div class="countdown"></div>';
            } else {
                echo '<div class="price">$' . $row["Price"] . '</div>';
            }

            echo '<div class="made-in">Made In: ' . $row["Made_In"] . '</div>';
            echo '<div class="department">Department: ' . $row["Department_Code"] . '</div>';

            $average_rating = round($row["Avg_Rating"], 1);
            echo '<div class="rating">Rating: ' . str_repeat('⭐', round($average_rating)) . ' (' . $row["Total_Reviews"] . ' reviews)</div>';
            echo '<a href="leave_review.php?item_id=' . $row["Item_Id"] . '"><button>Leave a Review</button></a>';
            echo '</div>'; 
        }

    } else {
        echo "No items found.";
    }
    ?>
    </div> 
    </main>

    <script>
        function allowDrop(event) {
            event.preventDefault();
        }

        function drop(event) {
            event.preventDefault();

            var data = event.dataTransfer.getData("text");
            var item = JSON.parse(data);

            var cartItems = JSON.parse(localStorage.getItem("cart")) || [];

            var itemIndex = cartItems.findIndex(i => i.id === item.id);
            if (itemIndex === -1) {
                item.quantity = 1;
                cartItems.push(item);
            } else {
                cartItems[itemIndex].quantity += 1; 
            }

            localStorage.setItem("cart", JSON.stringify(cartItems));

            updateCartUI();
        }

        function updateCartUI() {
            const cartItems = JSON.parse(localStorage.getItem("cart")) || [];
            const cartCount = cartItems.reduce((acc, item) => acc + item.quantity, 0); 
            document.getElementById("cart-count").textContent = cartCount;

            const cartItemsList = document.getElementById("cart-items");
            cartItemsList.innerHTML = ''; 

            cartItems.forEach(item => {
                const li = document.createElement("li");
                li.innerHTML = `${item.name} - $${item.price} x ${item.quantity}`;
                cartItemsList.appendChild(li);
            });
        }

        // Make items draggable
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector(".listProduct").addEventListener("dragstart", function (event) {
                if (event.target.classList.contains("item")) {
                    const itemData = {
                        id: event.target.getAttribute("data-id"),
                        name: event.target.getAttribute("data-name"),
                        price: event.target.getAttribute("data-price"),
                        image: event.target.getAttribute("data-image")
                    };
                    event.dataTransfer.setData("text", JSON.stringify(itemData));
                }
            });
        });
        
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.item').forEach(startCountdown);
        });


        function startCountdown(item) {
            const saleEndTime = item.getAttribute('data-sale-end-time');
            if (saleEndTime) {
                const saleEndDate = new Date(saleEndTime);
                const countdownElement = item.querySelector('.countdown');
                if (countdownElement) {
                    setInterval(() => {
                        const now = new Date();
                        const remainingTime = saleEndDate - now;

                        if (remainingTime <= 0) {
                            countdownElement.textContent = "Sale Ended";
                            return;
                        }

                        const hours = Math.floor((remainingTime / (1000 * 60 * 60)) % 24);
                        const minutes = Math.floor((remainingTime / (1000 * 60)) % 60);
                        const seconds = Math.floor((remainingTime / 1000) % 60);

                        countdownElement.textContent = `${hours}h ${minutes}m ${seconds}s`;
                    }, 1000);
                }
            }
        }


        // Load cart count on page load
        updateCartUI();
    </script>
</body>
</html>
