<?php
// Start session if needed
session_start();
include 'includes/db.php';

$message = "";

// Check if token is present
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Find user with this token
        $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE verification_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['is_verified']) {
                $message = "Your account is already verified. You can <a href='login.php'>login here</a>.";
            } else {
                // Update is_verified
                $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
                $update->execute([$user['id']]);
                $message = "Your email has been verified successfully! You can now <a href='login.php'>login</a>.";
            }
        } else {
            $message = "Invalid or expired verification link.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
} else {
    $message = "No verification token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Email Verification | Dog Adoption Center</title>
<link rel="stylesheet" href="assets/css/verify.css">
</head>
<body>

<div class="verify-container">
    <h1>Email Verification</h1>
    <div class="message"><?php echo $message; ?></div>
</div>

</body>
</html>
