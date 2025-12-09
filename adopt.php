<?php

session_start();
include 'includes/db.php'; // PDO connection

// ===== Get dog ID from URL =====
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: Invalid dog selection.");
}
$dogId = (int)$_GET['id'];

// ===== Load dog info from XML =====
$dogs = simplexml_load_file('xml/dogs.xml') or die("Error: Cannot load XML file");
$selectedDog = null;
foreach ($dogs->dog as $dog) {
    if ((int)$dog->id === $dogId) {
        $selectedDog = $dog;
        break;
    }
}
if ($selectedDog === null) die("Dog not found.");

$errorMsg = '';
$successMsg = '';

// ===== Check login =====
if (!isset($_SESSION['user_id'])) {
    // Redirect to login with "next" parameter
    $currentUrl = $_SERVER['REQUEST_URI'];
    header('Location: login.php?next=' . urlencode($currentUrl));
    exit();
}

$user_id = (int)$_SESSION['user_id'];

// ===== Check if user is verified =====
$stmtUser = $conn->prepare("SELECT is_verified FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user['is_verified']) {
    $_SESSION['notice'] = "You need to verify your email before submitting an adoption request.";
    $currentUrl = $_SERVER['REQUEST_URI'];
    header('Location: login.php?next=' . urlencode($currentUrl));
    exit();
}

// ===== Handle adoption request =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Check dog status
        $stmt = $conn->prepare("SELECT status FROM dogs WHERE id = ?");
        $stmt->execute([$dogId]);
        $dog = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dog) {
            $errorMsg = "Dog not found in database.";
        } elseif ($dog['status'] === 'Adopted') {
            $errorMsg = "Sorry — this dog is already adopted.";
        } else {
            // Insert adoption request
            $stmt2 = $conn->prepare("INSERT INTO adoptions (user_id, dog_id) VALUES (?, ?)");
            if ($stmt2->execute([$user_id, $dogId])) {
                $successMsg = "Your adoption request has been submitted successfully! 🐾";

                // Optional: mark dog as adopted
                $stmt3 = $conn->prepare("UPDATE dogs SET status = 'Adopted' WHERE id = ?");
                $stmt3->execute([$dogId]);
            } else {
                $errorMsg = "Database error: unable to submit adoption.";
            }
        }

    } catch (PDOException $e) {
        $errorMsg = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adopt <?php echo htmlspecialchars($selectedDog->name); ?> 🐾</title>
<link rel="stylesheet" href="assets/css/adopt.css">
</head>
<body>

<section class="adopt-form-section">
    <h1>Adopt <?php echo htmlspecialchars($selectedDog->name); ?> 🐾</h1>

    <!-- Messages -->
    <?php if ($errorMsg): ?>
        <div class="message error"><?php echo $errorMsg; ?></div>
    <?php endif; ?>
    <?php if ($successMsg): ?>
        <div class="message success"><?php echo $successMsg; ?></div>
    <?php endif; ?>

    <!-- Dog Info -->
    <div class="dog-info">
        <img src="assets/images/dogs/<?php echo htmlspecialchars($selectedDog->image); ?>" alt="<?php echo htmlspecialchars($selectedDog->name); ?>">
        <p><strong>Breed:</strong> <?php echo htmlspecialchars($selectedDog->breed); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($selectedDog->age); ?> years</p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($selectedDog->gender); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($selectedDog->status); ?></p>
        <p><?php echo htmlspecialchars($selectedDog->description); ?></p>
    </div>

    <!-- Form only shows if adoption not submitted -->
    <?php if (!$successMsg): ?>
    <form action="" method="POST">
        <button type="submit">Submit Adoption Request</button>
    </form>
    <?php endif; ?>
</section>

</body>
</html>
