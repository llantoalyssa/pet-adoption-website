<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Soft delete user
    $stmt = $conn->prepare("UPDATE users SET is_deleted = 1 WHERE id = ?");
    $stmt->execute([$user_id]);

    // Log out user
    session_destroy();

    // Redirect with flash message
    session_start();
    $_SESSION['flash'] = "Your account has been deleted successfully.";
    $_SESSION['flash_type'] = "success";

    header("Location: index.php");
    exit();
} catch(PDOException $e) {
    die("Error deleting account: " . $e->getMessage());
}
?>
