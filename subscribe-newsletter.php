<?php
session_start();
include 'includes/db.php';

// ===== PHPMailer =====
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // ===== Validate email =====
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash'] = "Please enter a valid email address.";
        $_SESSION['flash_type'] = "error";
        header("Location: index.php");
        exit();
    }

    try {
        // ===== Insert subscriber into database =====
        $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);

        // ===== Send confirmation email =====
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dogsadoptionwebsite@gmail.com'; // SMTP username
            $mail->Password   = 'sfckkpfxplqkaevq';    // SMTP password
            $mail->SMTPSecure = 'tls';                   // or 'ssl'
            $mail->Port       = 587;                     // or 465 for SSL

            //Recipients
            $mail->setFrom('dogsadoptionwebsite@gmail.com', 'Dog Adoption Center');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Subscription Confirmation';
            $mail->Body    = "Hi!<br><br>Thank you for subscribing to the Dog Adoption Center weekly newsletter.<br>You'll now receive weekly updates and news.<br><br>🐾 Happy tail wags!";

            $mail->send();
        } catch (Exception $e) {
            // Log the error (optional)
            error_log("Newsletter email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        $_SESSION['flash'] = "Thank you for subscribing! A confirmation email has been sent.";
        $_SESSION['flash_type'] = "success";

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // duplicate email
            $_SESSION['flash'] = "You are already subscribed.";
            $_SESSION['flash_type'] = "info";
        } else {
            $_SESSION['flash'] = "Something went wrong. Please try again.";
            $_SESSION['flash_type'] = "error";
        }
    }

    header("Location: index.php");
    exit();
}
