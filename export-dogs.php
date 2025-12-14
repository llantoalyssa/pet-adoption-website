<?php
// Include database connection
include 'includes/db.php';

// Set content type to XML
header('Content-Type: application/xml; charset=utf-8');

// Create root XML element
$xml = new SimpleXMLElement('<dogs/>');

try {
    // Fetch all dogs from database
    $stmt = $conn->query("SELECT id, name, breed, age, gender, status, image, description FROM dogs ORDER BY id DESC");
    $dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build XML
    foreach ($dogs as $dog) {
        $dogNode = $xml->addChild('dog');
        $dogNode->addChild('id', $dog['id']);
        $dogNode->addChild('name', htmlspecialchars($dog['name']));
        $dogNode->addChild('breed', htmlspecialchars($dog['breed']));
        $dogNode->addChild('age', $dog['age']);
        $dogNode->addChild('gender', htmlspecialchars($dog['gender']));
        $dogNode->addChild('status', htmlspecialchars($dog['status']));
        $dogNode->addChild('image', htmlspecialchars($dog['image']));
        $dogNode->addChild('description', htmlspecialchars($dog['description']));
    }

    // Output XML
    echo $xml->asXML();

} catch (PDOException $e) {
    // Output error as XML
    $error = $xml->addChild('error', htmlspecialchars($e->getMessage()));
    echo $xml->asXML();
}
