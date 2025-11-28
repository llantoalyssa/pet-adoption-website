<?php
// Include database connection (if needed later)
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
    <title>Dog Adoption Center</title>
    <!-- CSS -->
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
            <li><a href="#home"><strong>Home</strong></a></li>
            <li><a href="#dogs"><strong>Dogs</strong></a></li>
            <li><a href="#contact"><strong>Contact</strong></a></li>
        </ul>
    </nav>
</header>

<!-- HERO SECTION -->
<section id="home" class="hero">
    <h1>Welcome to the Dog Adoption Center!</h1>
    <p>Find your perfect furry friend today!</p>
</section>

<!-- DOGS SECTION -->
<section id="dogs" class="dogs-section">
    <h2>Available Dogs for Adoption</h2>
    <div class="dogs-container">
        <?php foreach ($dogs->dog as $dog): ?>
            <?php
                // Get current status from database
                $stmt = $conn->prepare("SELECT status FROM dogs WHERE id = ?");
                $stmt->execute([$dog->id]);
                $dbDog = $stmt->fetch(PDO::FETCH_ASSOC);
                $status = $dbDog ? $dbDog['status'] : trim($dog->status);

                // Determine link and class based on status
                $dogLink = ($status === 'Available') ? "adopt.php?id={$dog->id}" : "#";
                $linkClass = ($status === 'Available') ? "dog-link" : "dog-link-disabled";
        ?>
        <div class="dog-card">
            <!-- Clickable image + name -->
            <?php if ($status === 'Available'): ?>
                <a href="adopt.php?id=<?php echo $dog->id; ?>" class="dog-link">
                    <img src="assets/images/dogs/<?php echo $dog->image; ?>" alt="<?php echo $dog->name; ?>">
                    <h3><?php echo $dog->name; ?></h3>
                </a>
            <?php else: ?>
                <span class="dog-link-disabled">
                    <img src="assets/images/dogs/<?php echo $dog->image; ?>" alt="<?php echo $dog->name; ?>">
                    <h3><?php echo $dog->name; ?></h3>
                </span>
            <?php endif; ?>


            <p><strong>Breed:</strong> <?php echo $dog->breed; ?></p>
            <p><strong>Age:</strong> <?php echo $dog->age; ?> years</p>
            <p><strong>Gender:</strong> <?php echo $dog->gender; ?></p>
            <p><?php echo $dog->description; ?></p>

            <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>

            <?php if ($status === 'Available'): ?>
                <a href="adopt.php?id=<?php echo $dog->id; ?>" class="adopt-btn">Adopt Me</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Optional: View All Dogs -->
    <div class="view-all-dogs" style="text-align:center; margin-top:30px;">
        <a href="dogs.php" class="btn-view-all">View All Dogs</a>
    </div>
</section>

<!-- CONTACT / NEWSLETTER -->
<section id="contact" class="contact-section">
    <h2>Contact Us</h2>
    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <button type="submit">Subscribe to Newsletter</button>
    </form>
</section>

<!-- FOOTER -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Dog Adoption Center. All rights reserved.</p>
</footer>

<!-- TIDIO CHAT -->
<script src="//code.tidio.co/auxl5aeuqgmspfmkurizoujw55pzuiny.js" async></script>
<script src="assets/js/main.js"></script>
</body>
</html>


