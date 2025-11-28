<?php
// Database connection
include 'includes/db.php';

// Get dog ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Error: No dog selected.");
}
$dogId = (int)$_GET['id'];

// Get dog info from database
$stmt = $conn->prepare("SELECT * FROM dogs WHERE id = ?");
$stmt->execute([$dogId]);
$dog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dog) {
    die("Error: Dog not found.");
}
$status = $dog['status'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($dog['name']); ?> | Dog Adoption</title>
<link rel="stylesheet" href="assets/css/dog.css">
</head>
<body>

<section class="dog-profile">
    <div class="dog-image">
        <img src="assets/images/dogs/<?php echo htmlspecialchars($dog['image']); ?>" alt="<?php echo htmlspecialchars($dog['name']); ?>">
    </div>

    <div class="dog-details">
        <h1><?php echo htmlspecialchars($dog['name']); ?></h1>
        <p><strong>Breed:</strong> <?php echo htmlspecialchars($dog['breed']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($dog['age']); ?> years</p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars($dog['gender']); ?></p>
        <p><?php echo htmlspecialchars($dog['description']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>

        <?php if ($status === 'Available'): ?>
            <a href="adopt.php?id=<?php echo $dog['id']; ?>" class="adopt-btn">Adopt Now</a>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
