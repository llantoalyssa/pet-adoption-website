<?php
// Load dogs XML
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
