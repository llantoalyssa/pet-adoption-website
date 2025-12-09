<?php
session_start();

// Include database connection
include 'includes/db.php';

// Load dog data from XML (fallback)
$xmlFile = 'xml/dogs.xml';
$dogs = simplexml_load_file($xmlFile) or die("Error: Cannot load XML file");

// Handle flash notice
if (isset($_SESSION['notice'])) {
    $notice = $_SESSION['notice'];
    unset($_SESSION['notice']); // show only once
} else {
    $notice = "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dog Adoption Center</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- FLASH NOTICE -->
<?php if($notice): ?>
    <div class="notice"><?php echo htmlspecialchars($notice); ?></div>
<?php endif; ?>

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

        <?php if(isset($_SESSION['user_id'])): ?>
        <div style="position: relative; display: inline-block; margin-left: 20px;">

            <!-- BURGER BUTTON -->
            <button id="burger-btn" style="font-size:20px; cursor:pointer;">☰</button>

            <!-- DROPDOWN MENU -->
            <div id="dropdown-menu" style="display:none; position:absolute; right:0; background:#fff; border:1px solid #ccc; border-radius:5px; min-width:150px; box-shadow:0 2px 5px rgba(0,0,0,0.3);">
                <a href="logout.php" style="display:block; padding:10px; text-decoration:none; color:#333;">Logout</a>
                <form action="delete-account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
                    <button type="submit" style="width:100%; padding:10px; border:none; background:none; text-align:left; cursor:pointer; color:red;">Delete Account</button>
                </form>
            </div>
        </div>

        <script>
        // toggle dropdown when burger clicked
        document.getElementById('burger-btn').addEventListener('click', function() {
            const menu = document.getElementById('dropdown-menu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        // close dropdown if clicked outside
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('dropdown-menu');
            const btn = document.getElementById('burger-btn');
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
        </script>
        <?php else: ?>
            <li><a href="login.php"><strong>Login</strong></a></li>
        <?php endif; ?>
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

                // Determine adoption link
                if ($status === 'Available') {
                    if (isset($_SESSION['user_id'])) {
                        $dogLink = "adopt.php?id={$dog->id}";
                    } else {
                        // Redirect to login with next parameter
                        $dogLink = "login.php?next=" . urlencode("adopt.php?id={$dog->id}");
                    }
                } else {
                    $dogLink = "#"; // adopted dogs are not clickable
                }

                $linkClass = ($status === 'Available') ? "dog-link" : "dog-link-disabled";
            ?>
            <div class="dog-card">
                <!-- Clickable image + name -->
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

<?php if (isset($_SESSION['flash'])): ?>
    <div id="flash-message" class="flash-<?php echo $_SESSION['flash_type']; ?>">
        <?php echo $_SESSION['flash']; ?>
    </div>

    <style>
        #flash-message {
        position: fixed;
        top: 20px;
        left: 50%;                  /* center horizontally */
        transform: translateX(-50%) translateY(-100%); /* start above view */
        padding: 15px 20px;
        border-radius: 8px;
        background: #4CAF50;
        color: white;
        font-weight: bold;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        transition: all 0.5s ease;
        z-index: 9999;
        opacity: 0;
        }
        
        #flash-message.error {
            background: #e74c3c;
        }

        #flash-message.show {
            transform: translateX(-50%) translateY(0); /* slide into view */
            opacity: 1;
        }
    </style>

    <script>
        const flash = document.getElementById('flash-message');
        if (flash) {
            flash.classList.add('show');
            setTimeout(() => {
                flash.classList.remove('show');
            }, 3000);
        }
    </script>

<?php 
    unset($_SESSION['flash'], $_SESSION['flash_type']);
?>
<?php endif; ?>

</body>
</html>
