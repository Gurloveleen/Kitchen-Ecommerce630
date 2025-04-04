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

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    die("Unauthorized access. Please log in.");
}

// Retrieve user session data
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    die("Unauthorized access.");
}


// Fetch users for editing
$users_result = $conn->query("SELECT user_id, Name, Email, Admin FROM Users");

// Fetch items for editing
$items_result = $conn->query("SELECT Item_Id, Item_Name, Price, Made_In, Department_Code FROM Items");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="mainstyles.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>

    <h2>Manage Users</h2>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Admin</th>
            <th>Actions <a href="add_user.php">(Add User)</a> </th>
        </tr>
        <?php while ($user = $users_result->fetch_assoc()): ?>
        <tr>
            <td><?= $user['user_id'] ?></td>
            <td><?= htmlspecialchars($user['Name']) ?></td>
            <td><?= htmlspecialchars($user['Email']) ?></td>
            <td><?= $user['Admin'] ? "Yes" : "No" ?></td>
            <td>
                <a href="edit_user.php?user_id=<?= $user['user_id'] ?>">Edit User</a> | 
                <a href="delete_user.php?user_id=<?= $user['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete User</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Manage Items</h2>
    <table border="1">
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Price</th>
            <th>Made In</th>
            <th>Department Code</th>
            <th>Actions <a href="add_item.php">(Add Item)</a> </th>
        </tr>
        <?php while ($item = $items_result->fetch_assoc()): ?>
        <tr>
            <td><?= $item['Item_Id'] ?></td>
            <td><?= htmlspecialchars($item['Item_Name']) ?></td>
            <td>$<?= number_format($item['Price'], 2) ?></td>
            <td><?= htmlspecialchars($item['Made_In']) ?></td>
            <td><?= htmlspecialchars($item['Department_Code']) ?></td>
            <td>
                <a href="edit_item.php?item_id=<?= $item['Item_Id'] ?>">Edit Item</a> | 
                <a href="delete_item.php?item_id=<?= $item['Item_Id'] ?>" onclick="return confirm('Are you sure?')">Delete Item</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
