<?php
header("Content-Type: text/xml; charset=UTF-8");

include 'includes/db.php';

try {
    // Fetch all adoptions with dog and user info
    $stmt = $conn->query("
        SELECT 
            a.id AS adoption_id,
            a.status,
            a.created_at,
            u.id AS user_id,
            u.username AS user_name,
            u.email AS user_email,
            d.id AS dog_id,
            d.name AS dog_name,
            d.breed AS dog_breed,
            d.age AS dog_age,
            d.gender AS dog_gender
        FROM adoptions a
        JOIN users u ON a.user_id = u.id
        JOIN dogs d ON a.dog_id = d.id
        ORDER BY a.created_at DESC
    ");

    $adoptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Start XML
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><adoptions></adoptions>');

    foreach ($adoptions as $row) {
        $adoptionNode = $xml->addChild('adoption');
        $adoptionNode->addChild('adoption_id', $row['adoption_id']);
        $adoptionNode->addChild('status', $row['status']);
        $adoptionNode->addChild('created_at', $row['created_at']);

        $userNode = $adoptionNode->addChild('user');
        $userNode->addChild('user_id', $row['user_id']);
        $userNode->addChild('username', htmlspecialchars($row['user_name']));
        $userNode->addChild('email', $row['user_email']);

        $dogNode = $adoptionNode->addChild('dog');
        $dogNode->addChild('dog_id', $row['dog_id']);
        $dogNode->addChild('name', htmlspecialchars($row['dog_name']));
        $dogNode->addChild('breed', htmlspecialchars($row['dog_breed']));
        $dogNode->addChild('age', $row['dog_age']);
        $dogNode->addChild('gender', $row['dog_gender']);
    }

    echo $xml->asXML();

} catch (PDOException $e) {
    $errorXml = new SimpleXMLElement('<adoptions></adoptions>');
    $errorXml->addChild('error', htmlspecialchars($e->getMessage()));
    echo $errorXml->asXML();
}
