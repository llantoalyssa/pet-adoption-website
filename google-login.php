<?php
session_start();
require_once __DIR__ . '/composer-main/vendor/autoload.php';

$client = new Google_Client();
echo "Google_Client loaded!";
$client->setClientId('639656142392-6o2cgpl63rq1in7nhi3fqlt11njkvs60.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/pet-adoption-website/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();
header("Location: " . $login_url);
exit();
?>
