<?php
// process-adoption.php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['adoption_id'], $_POST['dog_id'])) {
    $action = $_POST['action'];
    $adoption_id = intval($_POST['adoption_id']);
    $dog_id = intval($_POST['dog_id']);

    try {
        if ($action === 'approve') {
            // 1. Update adoption status
            $stmt = $conn->prepare("UPDATE adoptions SET status = 'Approved' WHERE id = ?");
            $stmt->execute([$adoption_id]);

            // 2. Mark dog as Adopted
            $stmt = $conn->prepare("UPDATE dogs SET status = 'Adopted' WHERE id = ?");
            $stmt->execute([$dog_id]);

        } elseif ($action === 'reject') {
            // 1. Update adoption status to Rejected
            $stmt = $conn->prepare("UPDATE adoptions SET status = 'Rejected' WHERE id = ?");
            $stmt->execute([$adoption_id]);

            // 2. Ensure dog remains Available
            $stmt = $conn->prepare("UPDATE dogs SET status = 'Available' WHERE id = ?");
            $stmt->execute([$dog_id]);
        }

        // Redirect back to admin dashboard with message
        header("Location: admin-dashboard.php");
        exit();

    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

} else {
    die("Invalid request.");
}
