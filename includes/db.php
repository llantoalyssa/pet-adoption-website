<?php
// Database configuration

// LOCALHOST SETTINGS
$local_host = 'localhost';
$local_db   = 'dog_adoption_db';
$local_user = 'dogs_adopt_user';
$local_pass = 'DogsAdoptionCent3r';

// LIVE SERVER SETTINGS (replace these when you deploy)
$live_host = 'your_live_host';
$live_db   = 'your_live_db';
$live_user = 'your_live_user';
$live_pass = 'your_live_password';

// Detect environment
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'localhost') {
    $host = $local_host;
    $db   = $local_db;
    $user = $local_user;
    $pass = $local_pass;
} else {
    $host = $live_host;
    $db   = $live_db;
    $user = $live_user;
    $pass = $live_pass;
}

// Create PDO connection with UTF-8 and proper error handling
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
    ];
    
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // You can log the error for debugging instead of displaying it on production
    die("Database connection failed: " . $e->getMessage());
}
?>
