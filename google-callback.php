<?php
session_start();
require_once __DIR__ . '/composer-main/vendor/autoload.php';
require_once 'includes/db.php'; // updated database connection

$client = new Google_Client();
$client->setClientId('639656142392-6o2cgpl63rq1in7nhi3fqlt11njkvs60.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/pet-adoption-website/google-callback.php');

$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $google_service = new Google_Service_Oauth2($client);
        $data = $google_service->userinfo->get();

        // Get Google user data
        $google_id = $data['id'];
        $name      = $data['name'];
        $email     = $data['email'];
        $profile   = $data['picture'];

        // Check if user exists in DB and is not deleted
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? AND is_deleted = 0");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Insert new Google user with is_deleted = 0
            $insert = $conn->prepare(
                "INSERT INTO users (username, email, google_id, picture, created_at, is_verified, is_deleted) 
                 VALUES (?, ?, ?, ?, NOW(), 1, 0)"
            );
            $insert->execute([$name, $email, $google_id, $profile]);

            $user_id = $conn->lastInsertId();
            $username = $name;
        } else {
            $user_id = $user['id'];
            $username = $user['username'];
        }

        // Set session like normal login
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['login_type'] = "google";

        // Flash message
        $_SESSION['flash'] = "Successfully logged in with Google!";
        $_SESSION['flash_type'] = "success";

        // Redirect to homepage
        header("Location: index.php");
        exit();
    }
}

// If login fails, redirect to login page
header("Location: login.php");
exit();
?>
