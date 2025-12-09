<?php
session_start();

// Remove user login info only
unset($_SESSION['user_id']);
unset($_SESSION['username']);

// Set flash message
$_SESSION['flash'] = "You have successfully logged out.";
$_SESSION['flash_type'] = "success";

// Redirect to homepage
header("Location: index.php");
exit();
?>
