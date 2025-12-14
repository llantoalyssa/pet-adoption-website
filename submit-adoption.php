<?php
session_start();
include 'includes/db.php';

// ===== Enable PDO exceptions =====
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ===== Login check =====
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ===== Ensure POST request =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dogs.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$dog_id = isset($_POST['dog_id']) ? (int) $_POST['dog_id'] : 0;

// ===== Basic validation =====
$required_fields = ['full_name', 'phone', 'address', 'reason', 'living_situation'];
foreach ($required_fields as $field) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $_SESSION['flash'] = "Please fill all required fields.";
        $_SESSION['flash_type'] = "error";
        header("Location: adopt.php?id=$dog_id");
        exit();
    }
}

try {
    $conn->beginTransaction(); // Start transaction

    // ===== Validate dog =====
    $stmt = $conn->prepare("SELECT status FROM dogs WHERE id = ? FOR UPDATE");
    $stmt->execute([$dog_id]);
    $dog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dog || $dog['status'] !== 'Available') {
        $_SESSION['flash'] = "Dog is no longer available.";
        $_SESSION['flash_type'] = "error";
        $conn->rollBack();
        header("Location: dogs.php");
        exit();
    }

    // ===== Prevent duplicate requests =====
    $stmt = $conn->prepare("SELECT id FROM adoptions WHERE user_id = ? AND dog_id = ? AND status = 'Pending'");
    $stmt->execute([$user_id, $dog_id]);
    if ($stmt->fetch()) {
        $_SESSION['flash'] = "You already submitted a request for this dog.";
        $_SESSION['flash_type'] = "error";
        $conn->rollBack();
        header("Location: dogs.php");
        exit();
    }

    // ===== Insert adoption request =====
    $stmt = $conn->prepare("INSERT INTO adoptions (user_id, dog_id, status) VALUES (?, ?, 'Pending')");
    $stmt->execute([$user_id, $dog_id]);
    $adoption_id = $conn->lastInsertId();

    // ===== Insert adoption form =====
    $stmt = $conn->prepare("
        INSERT INTO adoption_forms 
        (adoption_id, full_name, phone, address, reason, experience, living_situation)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $adoption_id,
        trim($_POST['full_name']),
        trim($_POST['phone']),
        trim($_POST['address']),
        trim($_POST['reason']),
        trim($_POST['experience'] ?? ''),
        trim($_POST['living_situation'])
    ]);

    $conn->commit();

    $_SESSION['flash'] = "Adoption request submitted successfully!";
    $_SESSION['flash_type'] = "success";

} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['flash'] = "Error submitting adoption request. Please try again.";
    $_SESSION['flash_type'] = "error";
    error_log("Adoption submit error: " . $e->getMessage());
}

header("Location: dogs.php");
exit();
