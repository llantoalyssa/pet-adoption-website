<?php
session_start();
include 'includes/db.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $message = "Username or email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_token = bin2hex(random_bytes(16));

            $insert = $conn->prepare("INSERT INTO users (username, email, password, verification_token) VALUES (?, ?, ?, ?)");
            if ($insert->execute([$username, $email, $hashed_password, $verification_token])) {

                // Send verification email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dogsadoptionwebsite@gmail.com';
                    $mail->Password = 'sfck kpfx plqk aevq';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('dogsadoptionwebsite@gmail.com', 'Dog Adoption Center');
                    $mail->addAddress($email, $username);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email';
                    $verifyLink = "http://localhost/pet-adoption-website/verify.php?token=".$verification_token;
                    $mail->Body = "Hi $username,<br><br>Click this link to verify your email: <a href='$verifyLink'>Verify Email</a>";

                    $mail->send();
                    $_SESSION['flash'] = "Registration successful! Please check your email to verify your account.";
                    $_SESSION['flash_type'] = "success";

                    // Redirect to login page
                    header("Location: login.php");
                    exit();

                } catch (Exception $e) {
                    $message = "Registration successful but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Dog Adoption Center</title>
<link rel="stylesheet" href="assets/css/register.css">
</head>
<body>

<a href="index.php" style="position: fixed; top: 10px; left: 10px; z-index: 100;">
    <img src="assets/images/Paw House Logo.png" alt="Logo" 
         style="width: 55px; height: 55px; cursor: pointer;">
</a>

<div class="register-container">
    <h1>Create Account</h1>
    <?php if(!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

</body>
</html>
