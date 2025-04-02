<?php
$password = "sports";  // Replace with an actual user password
$salt = "04c43e35510727d8";   // Replace with an actual user's salt
echo md5($salt . $password);
?>