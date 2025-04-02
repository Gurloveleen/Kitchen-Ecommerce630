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

$admin = false; // Default value

if (isset($_SESSION['user']) && isset($_SESSION['user']['admin'])) {
    $admin = (int) $_SESSION['user']['admin'] === 1; // Directly use the stored admin flag
}

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
        <div class="cart" id="cart" ondrop="drop(event)" ondragover="allowDrop(event)">
            <img src="images/cart.png" alt="Cart Icon" />
            <ul class="cart-items" id="cart-items">
            </ul>
        </div>

        <h2>Product List</h2>
        <div class="listProduct">
        <?php
        
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "ecommerce_db";
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM items";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                echo '<div class="item" draggable="true" data-id="' . $row["Item_Id"] . '" data-name="' . $row["Item_Name"] . '" data-price="' . $row["Price"] . '" data-image="' . $row["Image_URL"] . '">';
                echo '<img src="' . $row["Image_URL"] . '" alt="' . $row["Item_Name"] . '" width="200" height="200">'; // Display item image
                echo '<h2>' . $row["Item_Name"] . '</h2>'; // Display item name
                echo '<div class="price">$' . $row["Price"] . '</div>'; // Display item price
                echo '<div class="made-in">Made In: ' . $row["Made_In"] . '</div>'; // Display item origin
                echo '<div class="department">Department: ' . $row["Department_Code"] . '</div>'; // Display department code
                echo '</div>';
            }
        } else {
            echo "No items found.";
        }

        $conn->close();
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
                item.quantity = 1; // If the item is not in the cart, initialize the quantity
                cartItems.push(item);
            } else {
                cartItems[itemIndex].quantity += 1; // If item already exists, increase quantity
            }

            localStorage.setItem("cart", JSON.stringify(cartItems));

            updateCartUI();
        }

        // Update the cart count and display items
        function updateCartUI() {
            const cartItems = JSON.parse(localStorage.getItem("cart")) || [];
            const cartCount = cartItems.reduce((acc, item) => acc + item.quantity, 0); // Sum of all quantities
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
        document.querySelectorAll(".item").forEach(item => {
            item.addEventListener("dragstart", (event) => {
                const itemData = {
                    id: event.target.getAttribute("data-id"),
                    name: event.target.getAttribute("data-name"),
                    price: event.target.getAttribute("data-price"),
                    image: event.target.getAttribute("data-image")
                };
                event.dataTransfer.setData("text", JSON.stringify(itemData));
            });
        });

        // Load cart count on page load
        updateCartUI();
    </script>
</body>
</html>
