<?php
session_start();
include 'includes/db.php';

// ===== Login check =====
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my-adoptions.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$adoption_id = (int) ($_POST['adoption_id'] ?? 0);

try {
    // ===== Check adoption ownership & status =====
    $stmt = $conn->prepare("
        SELECT id, status 
        FROM adoptions 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$adoption_id, $user_id]);
    $adoption = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adoption || $adoption['status'] !== 'Pending') {
        $_SESSION['flash'] = "You cannot cancel this request.";
        $_SESSION['flash_type'] = "error";
        header("Location: my-adoptions.php");
        exit();
    }

    // ===== Delete adoption form =====
    $stmt = $conn->prepare("DELETE FROM adoption_forms WHERE adoption_id = ?");
    $stmt->execute([$adoption_id]);

    // ===== Delete adoption request =====
    $stmt = $conn->prepare("DELETE FROM adoptions WHERE id = ?");
    $stmt->execute([$adoption_id]);

    $_SESSION['flash'] = "Adoption request cancelled successfully.";
    $_SESSION['flash_type'] = "success";

} catch (PDOException $e) {
    $_SESSION['flash'] = "Error cancelling request.";
    $_SESSION['flash_type'] = "error";
}

header("Location: my-adoptions.php");
exit();
