<?php session_start(); 

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_SESSION['user'];

$branches_query = "SELECT Branch_Id, Branch_Name, Address FROM Branch";
$branches_result = $conn->query($branches_query);
$branches = [];
while ($branch = $branches_result->fetch_assoc()) {
    $branches[] = $branch;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="mainstyles.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyClYvSlHYsrJgaOdeDeSuRtM6ECjyu6qwk&libraries=places"></script>
    <script>
        let directionsService;
        let directionsRenderer;

        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10,
                center: {lat: 43.6532, lng: -79.3832} // Default to Toronto
            });
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer();
            directionsRenderer.setMap(map);
        }

        function calculateRoute() {
            var selectedBranch = document.getElementById('branch').value;
            var userAddress = document.getElementById('userAddress').value;

            if (!selectedBranch || !userAddress) {
                alert("Please select a branch and enter your address.");
                return;
            }

            var request = {
                origin: selectedBranch,
                destination: userAddress,
                travelMode: 'DRIVING'
            };

            directionsService.route(request, function(result, status) {
                if (status === 'OK') {
                    directionsRenderer.setDirections(result);
                    let distance = result.routes[0].legs[0].distance.value / 1000; // Convert meters to KM
                    document.getElementById('distance_km').value = distance.toFixed(2);
                } else {
                    alert('Could not calculate route: ' + status);
                }
            });
        }

        function saveTrip() {
            var source = document.getElementById('branch').value;
            var destination = document.getElementById('userAddress').value;
            var distance = document.getElementById('distance_km').value;
            var price = (distance * 1.5).toFixed(2); // Example price formula
            var userId = document.getElementById('user_id').value;

            if (!source || !destination || !distance) {
                alert("Please calculate the route first.");
                return;
            }

            let formData = new FormData();
            formData.append("source", source);
            formData.append("destination", destination);
            formData.append("distance", distance);
            formData.append("price", price);
            formData.append("user_id", userId);

            fetch("save_trip.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                window.location.href = "payments.php"; // Redirect after saving trip
            })
            .catch(error => console.error("Error:", error));
        }

        document.addEventListener("DOMContentLoaded", function() {
            function loadCart() {
                let cart = JSON.parse(localStorage.getItem("cart")) || [];
                const cartContainer = document.getElementById("cart-items");
                const cartCount = document.getElementById("cart-count");

                if (cart.length === 0) {
                    cartContainer.innerHTML = "<tr><td colspan='5'>Your cart is empty.</td></tr>";
                    document.getElementById("checkout").disabled = true;
                    cartCount.textContent = 0;
                    return;
                }

                cartContainer.innerHTML = "";
                cart.forEach((item, index) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td>$${item.price}</td>
                        <td>$${(item.price * item.quantity).toFixed(2)}</td>
                        <td><button class="remove-btn" data-index="${index}">Remove</button></td>
                    `;
                    cartContainer.appendChild(row);
                });

                cartCount.textContent = cart.reduce((acc, item) => acc + item.quantity, 0);
                document.getElementById("checkout").disabled = false;
            }

            function removeItem(index) {
                let cart = JSON.parse(localStorage.getItem("cart")) || [];
                index = parseInt(index); // Ensure index is a number
                if (!isNaN(index) && index >= 0 && index < cart.length) {
                    cart.splice(index, 1);
                    localStorage.setItem("cart", JSON.stringify(cart));
                    loadCart(); // Refresh the cart UI
                }
            }

            // Attach event listener to cart items (Event Delegation)
            document.getElementById("cart-items").addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-btn")) {
                    let index = event.target.getAttribute("data-index");
                    removeItem(index);
                }
            });

    loadCart(); // Load cart when page loads
});

    </script>
</head>
<body onload="initMap()">
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
                    <a href="logout.php">Log Out</a>
                <?php else: ?>
                    <a href="signup.html">Sign Up</a>
                    <a href="signin.html">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
        <br><br><br><br><h1>Your Cart</h1>
    </header>
    
    <main>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-items"></tbody>
        </table>


        <h3>Choose Delivery Option</h3>
        <label for="branch">Select Branch:</label>
        <select id="branch">
            <?php foreach ($branches as $branch): ?>
                <option value="<?= htmlspecialchars($branch['Address']) ?>">
                    <?= htmlspecialchars($branch['Branch_Name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="userAddress">Enter Your Address:</label>
        <input type="text" id="userAddress">
        <button type="button" onclick="calculateRoute()">Show Route</button>
        <div id="map" style="width:100%; height:400px;"></div>

        <input type="hidden" id="distance_km">
        <input type="hidden" id="user_id" value="<?= $user ?>">

        <button type="button" onclick="saveTrip()">Proceed to Checkout</button>
    </main>
</body>
</html>
