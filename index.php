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

<?php if (isset($_SESSION['flash'])): ?>
    <div id="newsletter-flash" style="
        padding:10px; text-align:center; border-radius:5px; margin:10px auto; width:fit-content;
        background:<?= ($_SESSION['flash_type'] === 'success') ? '#2ecc71' : (($_SESSION['flash_type'] === 'error') ? '#e74c3c' : '#3498db'); ?>;
        color:#fff;
    ">
        <?= htmlspecialchars($_SESSION['flash']); ?>
    </div>
<?php 
    unset($_SESSION['flash'], $_SESSION['flash_type']); 
endif; 
?>


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
        <li><a href="#contact"><strong>Subscribe</strong></a></li>
        <li><a href="facility-map.php"><strong>Facility Map</strong></a></li>

        <?php if(isset($_SESSION['user_id'])): ?>
        <div style="position: relative; display: inline-block; margin-left: 20px;">

            <!-- BURGER BUTTON -->
            <button id="burger-btn" style="font-size:20px; cursor:pointer;">☰</button>

            <!-- DROPDOWN MENU -->
            <div id="dropdown-menu" style="display:none; position:absolute; right:0; background:#fff; border:1px solid #ccc; border-radius:5px; min-width:150px; box-shadow:0 2px 5px rgba(0,0,0,0.3);">
                <a href="logout.php" style="display:block; padding:10px; text-decoration:none; color:#333;">Logout</a>
                <a href="my-adoptions.php"
                    style="display:block; padding:10px; text-decoration:none; color:#333; white-space:nowrap;">
                    My Adoption Requests
                </a>
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

<!-- Admin Password Modal -->
<div id="adminModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
  <div style="background:#fff3e0; padding:20px; border-radius:8px; max-width:400px; width:90%;">
    <h2 style="color:#e65100;">Admin Login</h2>
    <input type="password" id="adminPassword" placeholder="Enter admin password" 
           style="width:100%; padding:10px; margin-top:10px; border-radius:5px; border:1px solid #ffcc80;">
    <button onclick="checkAdminPassword()" 
            style="margin-top:10px; padding:10px 15px; background:#ffb74d; border:none; border-radius:5px; cursor:pointer;">Submit</button>
    <button onclick="closeAdminModal()" 
            style="margin-top:10px; padding:10px 15px; background:#ffcc80; border:none; border-radius:5px; cursor:pointer;">Cancel</button>
  </div>
</div>

<script>
document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key === '\\') {  // Alt + \
        document.getElementById('adminModal').style.display = 'flex';
    }
});

function closeAdminModal() {
    document.getElementById('adminModal').style.display = 'none';
}

function checkAdminPassword() {
    const password = document.getElementById('adminPassword').value;
    if(password === "pet.adoption0123") {
        alert("Access granted!");
        window.location.href = "admin-dashboard.php"; // change to your admin dashboard
    } else {
        alert("Incorrect password!");
    }
}
</script>





<!-- HERO SECTION -->
<section id="home" class="hero">
    <h1>Welcome to the Dog Adoption Center!</h1>
    <p>Find your perfect furry friend today!</p>
</section>

<!-- DOGS SECTION -->
<section id="dogs" class="dogs-section">
    <h2 style="color:#fff;">Available Dogs for Adoption</h2>
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
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

</section>
<!-- Optional: View All Dogs -->
    <div class="view-all-dogs" style="text-align:center; margin-top: 10px;">
        <a href="dogs.php" class="btn-view-all">View All Dogs</a>
    </div>

<!-- CONTACT / NEWSLETTER -->
<section id="contact" class="contact-section">
    <!-- Newsletter Subscription -->
    <h3>Subscribe to our Weekly Newsletter</h3>
    <form action="subscribe-newsletter.php" method="POST" style="display:flex; gap:10px; align-items:center; justify-content:center; margin-top:10px;">
        <input type="email" name="email" placeholder="Your email" required style="padding:8px; border-radius:5px; border:1px solid #ccc;">
        <button type="submit" style="padding:8px 12px; border:none; border-radius:5px; background:#2ecc71; color:#fff; cursor:pointer;">Subscribe</button>
    </form>
</section>

<!-- Social Share Buttons with Icons -->
<div style="text-align:center; background:#fff; padding:15px;">
    <h4 style="margin-bottom:10px;">Share our website:</h4>

    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://pet-adoption-website.online/') ?>" target="_blank"
       style="display:inline-block; margin:0 10px; width:40px; height:40px; background:#1877f2; border-radius:50%; text-align:center; line-height:40px; color:white; text-decoration:none; font-size:20px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="20px" height="20px">
            <path d="M22.675 0H1.325C.593 0 0 .593 0 1.325v21.351C0 23.407.593 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.894-4.788 4.66-4.788 1.325 0 2.464.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.716-1.795 1.765v2.314h3.587l-.467 3.622h-3.12V24h6.116C23.407 24 24 23.407 24 22.675V1.325C24 .593 23.407 0 22.675 0z"/>
        </svg>
    </a>

    <!-- X -->
    <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://pet-adoption-website.online/') ?>&text=Check out this amazing dog adoption site!" target="_blank"
       style="display:inline-block; margin:0 10px; width:40px; height:40px; background:#1da1f2; border-radius:50%; text-align:center; line-height:40px; color:white; text-decoration:none; font-size:20px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="20px" height="20px">
            <path d="M24 4.557a9.93 9.93 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724 9.864 9.864 0 0 1-3.127 1.195 4.916 4.916 0 0 0-8.384 4.482 13.944 13.944 0 0 1-10.125-5.14 4.822 4.822 0 0 0-.664 2.475 4.916 4.916 0 0 0 2.188 4.096 4.903 4.903 0 0 1-2.229-.616v.062a4.919 4.919 0 0 0 3.946 4.827 4.996 4.996 0 0 1-2.224.084 4.922 4.922 0 0 0 4.596 3.417 9.867 9.867 0 0 1-6.102 2.105c-.396 0-.788-.023-1.175-.068a13.945 13.945 0 0 0 7.557 2.212c9.054 0 14.002-7.496 14.002-13.986 0-.213-.004-.425-.014-.636A10.012 10.012 0 0 0 24 4.557z"/>
        </svg>
    </a>

    <!-- WhatsApp -->
    <a href="https://api.whatsapp.com/send?text=Check out this amazing dog adoption site! <?= urlencode('http://pet-adoption-website.online/') ?>" target="_blank"
       style="display:inline-block; margin:0 10px; width:40px; height:40px; background:#25D366; border-radius:50%; text-align:center; line-height:40px; color:white; text-decoration:none; font-size:20px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="20px" height="20px">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.161 1.24 8.413 3.492 2.251 2.252 3.492 5.232 3.492 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.518-5.688-1.5L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.885-4.434 9.888-9.884.002-2.637-1.03-5.106-2.887-6.958-1.856-1.853-4.325-2.884-6.961-2.885-5.449 0-9.885 4.434-9.888 9.884-.001 2.167.563 3.947 1.5 5.682l-.999 3.64 3.597-.936zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.148-1.758-.867-2.031-.967-.272-.099-.47-.148-.668.149-.198.297-.767.966-.94 1.164-.173.198-.347.223-.644.074-.297-.148-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.173.198-.297.297-.495.099-.198.05-.372-.025-.52-.074-.148-.668-1.611-.916-2.207-.242-.579-.487-.5-.668-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.148.198 2.095 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.227 1.36.195 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.29.173-1.413z"/>
        </svg>
    </a>
</div>



<!-- FOOTER -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> Dog Adoption Center. All rights reserved.</p>
</footer>



<!-- TIDIO CHAT -->
<script src="//code.tidio.co/ub4tjybbru1bd8uck2byrgaxqjtf0ztp.js" async></script>
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

<!-- WARNING ON LEAVING PAGE -->
<script>
let allowUnload = false;

window.addEventListener("beforeunload", function (e) {
    if (!allowUnload) {
        e.preventDefault();
        e.returnValue = "Changes may not be saved. Are you sure you want to leave?";
    }
});

// Allow navigation within the site without the warning
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        allowUnload = true;
    });
});
</script>

<script>
    const flash = document.getElementById('newsletter-flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = "opacity 0.5s ease";
            flash.style.opacity = "0";
            setTimeout(() => flash.remove(), 500); // remove from DOM after fade
        }, 2000); // 2000ms = 2 seconds
    }
</script>



</body>
</html>
