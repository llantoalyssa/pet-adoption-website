<?php
// Start session to get logged-in user info later
session_start();

// Make sure a dog is selected
if (!isset($_GET['id'])) {
    die("Error: No dog selected.");
}
$dogId = $_GET['id'];

// Optional: you can load the dog info from DB or XML if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt This Dog</title>
    <link rel="stylesheet" href="assets/css/adopt.css">
</head>
<body>

<section class="adopt-form-section">
    <h1>Adopt This Dog 🐾</h1>
    <form action="submit_adoption.php" method="POST">
        <!-- Hidden dog id -->
        <input type="hidden" name="dog_id" value="<?php echo $dogId; ?>">

        <!-- User Info -->
        <input type="text" name="name" placeholder="Your Full Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <input type="tel" name="phone" placeholder="Your Phone Number" required>

        <button type="submit">Submit Adoption Request</button>
    </form>
</section>

</body>
</html>
