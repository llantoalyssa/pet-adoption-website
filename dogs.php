<?php
// Include database connection
include 'includes/db.php';

// Load dog data from XML
$xmlFile = 'xml/dogs.xml';
$dogs = simplexml_load_file($xmlFile) or die("Error: Cannot load XML file");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt a Dog</title>
    <link rel="stylesheet" href="assets/css/dogs.css">
</head>
<body>

<header>
    <div class="title-row">
        <img src="assets/images/Paw House Logo.png" alt="Logo" class="dogs-logo">
        <h1>Meet Our Dogs</h1>
    </div>
    <p class="subtitle">Find your new best friend today!</p>
</header>

<section class="dogs-section">
    <div class="dogs-container">
        <?php foreach ($dogs->dog as $dog): ?>
            <div class="dog-card">
                <img src="assets/images/dogs/<?php echo htmlspecialchars($dog->image); ?>" alt="<?php echo htmlspecialchars($dog->name); ?>">
                
                <div class="dog-info">
                    <h2><?php echo htmlspecialchars($dog->name); ?></h2>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($dog->breed); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($dog->age); ?> years old</p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($dog->gender); ?></p>
                    <p><?php echo htmlspecialchars($dog->description); ?></p>

                    <?php
                    // Get current status from database
                    $stmt = $conn->prepare("SELECT status FROM dogs WHERE id = ?");
                    $stmt->execute([$dog->id]);
                    $dbDog = $stmt->fetch(PDO::FETCH_ASSOC);
                    $status = $dbDog ? $dbDog['status'] : trim($dog->status);
                    ?>

                    <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
                    <?php if ($status === 'Available'): ?>
                        <a href="adopt.php?id=<?php echo $dog->id; ?>" class="btn-view">Adopt Now</a>
                    <?php else: ?>
                        <span class="adopted-label">Already Adopted</span>
                    <?php endif; ?>

                    <a href="dog.php?id=<?php echo $dog->id; ?>" class="btn-view">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

</body>
</html>
