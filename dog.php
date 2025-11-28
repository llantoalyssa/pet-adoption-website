<?php
// Load dogs.xml
$xmlFile = 'xml/dogs.xml';
$dogs = simplexml_load_file($xmlFile) or die("Error: Cannot load XML file");

// Get dog ID from URL
if (!isset($_GET['id'])) {
    die("Error: No dog selected.");
}

$dogId = $_GET['id'];

// Find the selected dog
$selectedDog = null;
foreach ($dogs->dog as $dog) {
    if ((int)$dog->id == (int)$dogId) {
        $selectedDog = $dog;
        break;
    }
}

if ($selectedDog === null) {
    die("Error: Dog not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $selectedDog->name; ?> | Dog Adoption</title>
    <link rel="stylesheet" href="assets/css/dog.css">
</head>
<body>

<section class="dog-profile">
    <div class="dog-image">
        <img src="assets/images/dogs/<?php echo $selectedDog->image; ?>" alt="<?php echo $selectedDog->name; ?>">
    </div>
    <div class="dog-details">
        <h1><?php echo $selectedDog->name; ?></h1>
        <p><strong>Breed:</strong> <?php echo $selectedDog->breed; ?></p>
        <p><strong>Age:</strong> <?php echo $selectedDog->age; ?> years</p>
        <p><strong>Gender:</strong> <?php echo $selectedDog->gender; ?></p>
        <p><strong>Status:</strong> <?php echo $selectedDog->status; ?></p>
        <p><?php echo $selectedDog->description; ?></p>

        <a href="adopt.php?id=<?php echo $selectedDog->id; ?>" class="adopt-btn">Adopt Now</a>

    </div>
</section>

</body>
</html>
