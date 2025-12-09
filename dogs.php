<?php
session_start();

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
<title>All Dogs | Dog Adoption Center</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="logo">
        <img src="assets/images/Paw House with Text Logo.png" alt="Logo">
    </div>
    <nav>
        <ul>
            <li><a href="index.php"><strong>Home</strong></a></li>
            <li><a href="dogs.php"><strong>Dogs</strong></a></li>
            <li><a href="#contact"><strong>Contact</strong></a></li>
        </ul>
    </nav>
</header>

<!-- DOGS LISTING -->
<section class="dogs-section">
    <h2>All Dogs Available for Adoption</h2>
    <div class="dogs-container">
        <?php foreach ($dogs->dog as $dog): ?>
            <?php
                // Get current status from database
                $stmt = $conn->prepare("SELECT status FROM dogs WHERE id = ?");
                $stmt->execute([$dog->id]);
                $dbDog = $stmt->fetch(PDO::FETCH_ASSOC);
                $status = $dbDog ? $dbDog['status'] : trim($dog->status);

                // Determine link and class
                if ($status === 'Available') {
                    if (isset($_SESSION['user_id'])) {
                        $dogLink = "adopt.php?id={$dog->id}";
                    } else {
                        $dogLink = "login.php?next=" . urlencode("adopt.php?id={$dog->id}");
                    }
                } else {
                    $dogLink = "#"; // adopted
                }

                $linkClass = ($status === 'Available') ? "dog-link" : "dog-link-disabled";
            ?>
            <div class="dog-card">
                <a href="<?php echo $dogLink; ?>" class="<?php echo $linkClass; ?>">
                    <img src="assets/images/dogs/<?php echo $dog->image; ?>" alt="<?php echo $dog->name; ?>">
                    <h3><?php echo $dog->name; ?></h3>
                </a>

                <p><strong>Breed:</strong> <?php echo $dog->breed; ?></p>
                <p><strong>Age:</strong> <?php echo $dog->age; ?> years</p>
                <p><strong>Gender:</strong> <?php echo $dog->gender; ?></p>
                <p><?php echo $dog->description; ?></p>

                <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>

                <?php if ($status === 'Available'): ?>
                    <a href="<?php echo $dogLink; ?>" class="adopt-btn">Adopt Me</a>
                <?php else: ?>
                    <span class="adopted-label">Already Adopted</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Dog Adoption Center. All rights reserved.</p>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
