<?php
session_start();
include 'includes/db.php';

// ===== Check if admin =====
$isAdmin = isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';

// ===== Get user's pending adoptions =====
$userPendingDogs = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT dog_id FROM adoptions WHERE user_id = ? AND status = 'Pending'");
    $stmt->execute([$_SESSION['user_id']]);
    $userPendingDogs = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ===== Load dogs =====
if ($isAdmin) {
    $stmt = $conn->query("SELECT * FROM dogs ORDER BY id DESC");
} else {
    if (!empty($userPendingDogs)) {
        $placeholders = implode(',', array_fill(0, count($userPendingDogs), '?'));
        $sql = "SELECT * FROM dogs WHERE status = 'Available' OR id IN ($placeholders) ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute($userPendingDogs);
    } else {
        $stmt = $conn->query("SELECT * FROM dogs WHERE status = 'Available' ORDER BY id DESC");
    }
}

$dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===== Flash messages =====
$flashMsg = '';
$flashType = '';
if (isset($_SESSION['flash'])) {
    $flashMsg = $_SESSION['flash'];
    $flashType = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Dogs | Dog Adoption Center</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.pending-label { display:inline-block; padding:8px 12px; background:#f1c40f; color:#000; border-radius:5px; font-weight:bold; }
.adopted-label { display:inline-block; padding:8px 12px; background:#e74c3c; color:#fff; border-radius:5px; font-weight:bold; }
.flash { padding:10px; border-radius:5px; margin-bottom:20px; }
.flash.success { background-color: #2ecc71; color: #fff; }
.flash.error { background-color: #e74c3c; color: #fff; }
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="assets/images/Paw House with Text Logo.png" alt="Logo">
    </div>
    <nav>
        <ul>
            <li><a href="index.php"><strong>Home</strong></a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <?php if ($flashMsg): ?>
        <div class="flash <?= htmlspecialchars($flashType) ?>">
            <?= htmlspecialchars($flashMsg) ?>
        </div>
    <?php endif; ?>
</div>

<section class="dogs-section">
    <h2>Dogs for Adoption</h2>

    <div class="dogs-container">
        <?php if (count($dogs) === 0): ?>
            <p>No dogs available at the moment.</p>
        <?php endif; ?>

        <?php foreach ($dogs as $dog): 
            $status = $dog['status'];
            $isPending = isset($_SESSION['user_id']) && in_array($dog['id'], $userPendingDogs);

            if ($isPending) {
                $dogLink = "#";
                $linkClass = "dog-link-disabled";
            } elseif ($status === 'Available') {
                $dogLink = isset($_SESSION['user_id'])
                    ? "adopt.php?id=" . $dog['id']
                    : "login.php?next=" . urlencode("adopt.php?id=" . $dog['id']);
                $linkClass = "dog-link";
            } else {
                $dogLink = "#";
                $linkClass = "dog-link-disabled";
            }
        ?>
        <div class="dog-card">
            <a href="<?= $dogLink ?>" class="<?= $linkClass ?>">
                <img src="assets/images/dogs/<?= htmlspecialchars($dog['image']) ?>" alt="<?= htmlspecialchars($dog['name']) ?>">
                <h3><?= htmlspecialchars($dog['name']) ?></h3>
            </a>
            <p><strong>Breed:</strong> <?= htmlspecialchars($dog['breed']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($dog['age']) ?> years</p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($dog['gender']) ?></p>
            <p><?= htmlspecialchars($dog['description']) ?></p>

            <?php if ($isPending): ?>
                <span class="pending-label">Pending Approval</span>
            <?php elseif ($status === 'Available'): ?>
                <a href="<?= $dogLink ?>" class="adopt-btn">Adopt Me</a>
            <?php else: ?>
                <span class="adopted-label">Already Adopted</span>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<footer>
    <p>&copy; <?= date("Y"); ?> Dog Adoption Center. All rights reserved.</p>
</footer>
<script src="assets/js/main.js"></script>
<script>
  // Auto-hide flash message after 2 seconds (2000ms)
  setTimeout(function() {
    const flash = document.querySelector('.flash');
    if (flash) {
      flash.style.transition = 'opacity 0.5s';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 500); // remove from DOM after fade-out
    }
  }, 2000);
</script>
</body>
</html>
