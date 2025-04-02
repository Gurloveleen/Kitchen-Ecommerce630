<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Route</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyClYvSlHYsrJgaOdeDeSuRtM6ECjyu6qwk&libraries=places"></script>
    <link rel="stylesheet" href="mainstyles.css">
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
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
                    <a href="logout.php">Log Out</a>
                <?php else: ?>
                    <a href="signup.html">Sign Up</a>
                    <a href="signin.html">Sign In</a>
                <?php endif; ?>
            </div>
            
        </nav>
        <br><br><br><br><h1>Your Delivery Route</h1>
    </header>

    <main>
        <h2>Select a Branch for Delivery</h2>
        <select id="branch">
            <option value="43.6532,-79.3832">Toronto Branch</option>
            <option value="43.7001,-79.4163">North York Branch</option>
            <option value="43.5890,-79.6441">Mississauga Branch</option>
        </select>
        <br><br>
        
        <input type="text" id="customerAddress" placeholder="Enter delivery address">
        <button onclick="calculateRoute()">Show Route</button><br><br>
        
        <div id="map"></div>
    
        
        <br>
        <button id="checkoutBtn">Proceed to Checkout</button>
    
        <script>
            function initMap() {
                window.map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 43.7, lng: -79.4 }, 
                    zoom: 10
                });
                window.directionsService = new google.maps.DirectionsService();
                window.directionsRenderer = new google.maps.DirectionsRenderer();
                directionsRenderer.setMap(map);
            }
    
            function calculateRoute() {
                const selectedBranch = document.getElementById('branch').value.split(',');
                const customerAddress = document.getElementById('customerAddress').value;
    
                if (!customerAddress) {
                    alert("Please enter a delivery address.");
                    return;
                }
    
                const origin = new google.maps.LatLng(parseFloat(selectedBranch[0]), parseFloat(selectedBranch[1]));
    
                const request = {
                    origin: origin,
                    destination: customerAddress,
                    travelMode: 'DRIVING'
                };
    
                directionsService.route(request, function(result, status) {
                    if (status === 'OK') {
                        directionsRenderer.setDirections(result);
                    } else {
                        alert('Error: ' + status);
                    }
                });
            }
    
            document.getElementById("checkoutBtn").addEventListener("click", function() {
                window.location.href = "payments.php";
            });
    
            window.onload = initMap;
        </script>
    </main>
    
</body>
</html>