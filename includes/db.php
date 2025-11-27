<?php
$host = "localhost";
$dbname = "dog_adoption_db";
$username = "root"; // default XAMPP username
$password = "";     // default XAMPP password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: UTF-8 encoding
    $conn->exec("SET NAMES 'utf8'");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
