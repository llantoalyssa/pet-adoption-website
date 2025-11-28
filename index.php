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
    <title>Dog Adoption Center</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Header -->
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


<!-- Hero Section -->
<section id="home" class="hero">
    <h1>Welcome to the Dog Adoption Center!</h1>
    <p>Find your perfect furry friend today!</p>
</section>

<!-- Dogs Section -->
<section id="dogs" class="dogs-section">
    <h2>Available Dogs for Adoption</h2>
    <div class="dogs-container">
        <?php foreach ($dogs->dog as $dog): ?>
            <div class="dog-card">
                <img src="assets/images/dogs/<?php echo $dog->image; ?>" alt="<?php echo $dog->name; ?>">
                <h3><?php echo $dog->name; ?></h3>
                <p><strong>Breed:</strong> <?php echo $dog->breed; ?></p>
                <p><strong>Age:</strong> <?php echo $dog->age; ?> years</p>
                <p><strong>Gender:</strong> <?php echo $dog->gender; ?></p>
                <p><strong>Description:</strong> <?php echo $dog->description; ?></p>
                <p><strong>Status:</strong> <?php echo $dog->status; ?></p>
                <button class="adopt-btn">Adopt Me</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Contact / Newsletter -->
<section id="contact" class="contact-section">
    <h2>Contact Us</h2>
    <form action="register.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <button type="submit">Subscribe to Newsletter</button>
    </form>
</section>



<!-- Footer -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Dog Adoption Center. All rights reserved.</p>
</footer>

<!-- Tidio Live Chat -->
<script src="//code.tidio.co/auxl5aeuqgmspfmkurizoujw55pzuiny.js" async></script>
</body>
</html>

<script src="assets/js/main.js"></script>
