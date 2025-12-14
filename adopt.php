<?php
session_start();
include 'includes/db.php';

// ===== Check login =====
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?next=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
$user_id = (int) $_SESSION['user_id'];

// ===== Validate dog ID =====
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dogs.php");
    exit();
}
$dogId = (int) $_GET['id'];

// ===== Load dog from DATABASE =====
$stmt = $conn->prepare("SELECT * FROM dogs WHERE id = ?");
$stmt->execute([$dogId]);
$dog = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if dog exists and is available
if (!$dog) {
    $_SESSION['flash'] = "Dog not found.";
    $_SESSION['flash_type'] = "error";
    header("Location: dogs.php");
    exit();
}

// ===== Check if user already requested this dog =====
$stmt = $conn->prepare("SELECT * FROM adoptions WHERE user_id = ? AND dog_id = ? AND status = 'Pending'");
$stmt->execute([$user_id, $dogId]);
if ($stmt->fetch()) {
    $_SESSION['flash'] = "You already submitted a request for this dog.";
    $_SESSION['flash_type'] = "error";
    header("Location: dogs.php");
    exit();
}

// ===== Check email verification =====
$stmt = $conn->prepare("SELECT is_verified FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !$user['is_verified']) {
    $_SESSION['flash'] = "Please verify your email before adopting.";
    $_SESSION['flash_type'] = "error";
    header("Location: login.php?next=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adopt <?= htmlspecialchars($dog['name']) ?> 🐾</title>
<link rel="stylesheet" href="assets/css/adopt.css">
</head>
<body>

<section class="adopt-form-section">
    <h1>Adopt <?= htmlspecialchars($dog['name']) ?> 🐾</h1>

    <!-- Dog Info -->
    <div class="dog-info">
        <img src="assets/images/dogs/<?= htmlspecialchars($dog['image']) ?>" alt="<?= htmlspecialchars($dog['name']) ?>">
        <p><strong>Breed:</strong> <?= htmlspecialchars($dog['breed']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($dog['age']) ?> years</p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($dog['gender']) ?></p>
        <p><?= htmlspecialchars($dog['description']) ?></p>
    </div>

    <!-- Adoption Form -->
    <form action="submit-adoption.php" method="POST">
        <input type="hidden" name="dog_id" value="<?= $dog['id'] ?>">

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Phone Number</label>
        <input type="text" name="phone" required>

        <label>Address</label>
        <textarea name="address" required></textarea>

        <label>Why do you want to adopt this dog?</label>
        <textarea name="reason" required></textarea>

        <label>Experience with dogs</label>
        <textarea name="experience"></textarea>

        <label>Living Situation</label>
        <select name="living_situation" required>
            <option value="">Select</option>
            <option>House with yard</option>
            <option>House</option>
            <option>Apartment</option>
            <option>Condo</option>
        </select>

        <button type="submit">Submit Adoption Request</button>
    </form>

    
</section>

</body>
</html>
