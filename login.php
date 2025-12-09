<?php
session_start();
include 'includes/db.php';

$message = "";

// Preserve next parameter from GET
$next = isset($_GET['next']) ? $_GET['next'] : 'index.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_email = trim($_POST['username_email']);
    $password = $_POST['password'];
    $next = isset($_POST['next']) ? $_POST['next'] : 'index.php';

    if (empty($username_email) || empty($password)) {
        $message = "Both fields are required.";
    } else {
        // Check user credentials, only for active (not soft-deleted) users
        $stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_deleted = 0");
        $stmt->execute([$username_email, $username_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_verified']) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['flash'] = "Successfully logged in!";
                $_SESSION['flash_type'] = "success";

                header("Location: $next");
                exit();
            } else {
                $message = "You must verify your email before logging in.";
            }
        } else {
            $message = "Invalid username/email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Dog Adoption Center</title>
<link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">
    <h1>Login</h1>

    <!-- Display server-side message -->
    <?php if($message): ?>
        <div class="message error"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="text" name="username_email" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="next" value="<?php echo htmlspecialchars($next); ?>">
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
    
    <a href="google-login.php" 
       style="
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            width:100%;
            padding:10px 1px;
            background-color:#ffffff;
            border:1px solid #dadce0;
            border-radius:4px;
            text-decoration:none;
            color:#3c4043;
            font-size:14px;
            font-weight:500;
            cursor:pointer;
            transition:all 0.2s ease;
            margin-top:10px;
       "
       onmouseover="this.style.backgroundColor='#f7f8f8'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.15)';"
       onmouseout="this.style.backgroundColor='#ffffff'; this.style.boxShadow='none';"
    >
        <img src="assets/images/google.png" alt="Google Logo" style="width:40px; height:20px;">
        <span>Sign in with Google</span>
    </a>

</div>

<!-- Flash message JS -->
<?php if (isset($_SESSION['flash'])): ?>
    <div id="flash-message" class="flash-<?php echo $_SESSION['flash_type']; ?>">
        <?php echo $_SESSION['flash']; unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
    </div>

    <script>
    const flash = document.getElementById('flash-message');
    flash.classList.add('show');
    setTimeout(() => { flash.classList.remove('show'); }, 3000);
    </script>
<?php endif; ?>

</body>
</html>
