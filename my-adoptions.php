<?php
session_start();
include 'includes/db.php';

// ===== Login check =====
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// ===== Fetch user's adoptions =====
$stmt = $conn->prepare("
    SELECT 
        a.id AS adoption_id,
        a.status,
        a.created_at,
        d.name,
        d.breed,
        d.age,
        d.gender,
        d.image
    FROM adoptions a
    JOIN dogs d ON a.dog_id = d.id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
");
$stmt->execute([$user_id]);
$adoptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<title>My Adoptions</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.status-pending { color:#f39c12; font-weight:bold; }
.status-approved { color:#2ecc71; font-weight:bold; }
.status-rejected { color:#e74c3c; font-weight:bold; }

.adoption-card {
    border:1px solid #ddd;
    padding:15px;
    border-radius:8px;
    margin-bottom:15px;
    display:flex;
    gap:15px;
}

.adoption-card img {
    width:150px;
    border-radius:8px;
}

.flash {
    padding:10px;
    border-radius:5px;
    margin-bottom:15px;
}

.flash.success { background:#2ecc71; color:#fff; }
.flash.error { background:#e74c3c; color:#fff; }
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
            <li><a href="dogs.php"><strong>Dogs</strong></a></li>
            <li><a href="my-adoptions.php"><strong>My Adoptions Requests</strong></a></li>
        </ul>
    </nav>
</header>

<section class="container">
    <h2 style="color:#fff;">🐾 My Adoption Requests</h2>

    <?php if ($flashMsg): ?>
        <div class="flash <?= htmlspecialchars($flashType) ?>">
            <?= htmlspecialchars($flashMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (count($adoptions) === 0): ?>
        <p>You have not submitted any adoption requests yet.</p>
    <?php endif; ?>

    <?php foreach ($adoptions as $row): ?>
        <div class="adoption-card">
            <img src="assets/images/dogs/<?= htmlspecialchars($row['image']) ?>">
            <div>
                <h3><?= htmlspecialchars($row['name']) ?></h3>

                <p><strong>Breed:</strong> <?= htmlspecialchars($row['breed']) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?> years</p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>

                <p>
                    <strong>Status:</strong>
                    <span class="status-<?= strtolower($row['status']) ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>
                </p>

                <small>
                    Requested on: <?= date("F d, Y", strtotime($row['created_at'])) ?>
                </small>

                <!-- ===== Cancel Button (Pending only) ===== -->
                <?php if ($row['status'] === 'Pending'): ?>
                    <form action="cancel-adoption.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this adoption request?');">
                        <input type="hidden" name="adoption_id" value="<?= $row['adoption_id'] ?>">
                        <button type="submit"
                                style="margin-top:10px; background:#e74c3c; color:#fff; border:none; padding:8px 12px; border-radius:5px; cursor:pointer;">
                            Cancel Request
                        </button>

                    </form>
                <?php endif; ?>

            </div>
        </div>
    <?php endforeach; ?>
</section>

<footer>
    <p>&copy; <?= date("Y"); ?> Dog Adoption Center</p>
</footer>



</body>
</html>
