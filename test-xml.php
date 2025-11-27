<?php
$xml = simplexml_load_file('xml/dogs.xml') or die("Error: Cannot load XML file");
foreach ($xml->dog as $dog) {
    echo "Name: " . $dog->name . "<br>";
    echo "Breed: " . $dog->breed . "<br>";
    echo "Age: " . $dog->age . "<br>";
    echo "Gender: " . $dog->gender . "<br>";
    echo "Description: " . $dog->description . "<br>";
    echo "<img src='assets/images/dogs/" . $dog->image . "' alt='" . $dog->name . "' style='width:150px;'><hr>";
}
?>
