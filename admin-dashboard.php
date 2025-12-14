<?php
include 'includes/db.php';

$message = "";

// ================== Handle Dog Addition ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_dog') {
    $name = trim($_POST['name']);
    $breed = trim($_POST['breed']);
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "assets/images/dogs/";
        $image = basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath);
    }

    $stmt = $conn->prepare("INSERT INTO dogs (name, breed, age, gender, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $breed, $age, $gender, $description, $image, $status])) {
        $message = "Dog added successfully!";
    } else {
        $message = "Failed to add dog.";
    }
}

// ================== Handle Dog Status Update ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $dog_id = intval($_POST['dog_id']);
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE dogs SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $dog_id])) {
        $message = "Dog status updated successfully!";
    } else {
        $message = "Failed to update status.";
    }
}

// ================== Fetch All Dogs ==================
$stmt = $conn->query("SELECT * FROM dogs ORDER BY id DESC");
$dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================== Fetch Pending Adoption Requests ==================
$stmt = $conn->query("
    SELECT 
        a.id AS adoption_id,
        a.status,
        u.username,
        u.email,
        d.name AS dog_name,
        d.image,
        d.id AS dog_id
    FROM adoptions a
    JOIN users u ON a.user_id = u.id
    JOIN dogs d ON a.dog_id = d.id
    WHERE a.status = 'Pending'
    ORDER BY a.id DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Dog Adoption Center</title>
<style>
    body { font-family: Arial, sans-serif; background-color: #fff8f0; margin:0; padding:0; }
    header { background-color: #f7d794; padding:20px; text-align:center; font-size:24px; color:#6b4226; font-weight:bold; }
    .container { max-width: 900px; margin: 40px auto; background-color:#fff; padding:30px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
    h2 { color: #f19066; margin-bottom:20px; }
    form input, form select, form textarea, form button { width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:5px; font-size:14px; }
    form button { background-color: #f7d794; color:#6b4226; border:none; cursor:pointer; font-weight:bold; transition:all 0.2s ease; }
    form button:hover { background-color: #f19066; color:#fff; }
    .message { padding:10px; margin-bottom:20px; border-radius:5px; color:#fff; background-color:#6b4226; }
    table { width:100%; border-collapse:collapse; margin-top:30px; }
    table th, table td { padding:10px; border:1px solid #ddd; text-align:center; }
    table th { background-color:#f7d794; color:#6b4226; }
    select.status-select { padding:5px 10px; border-radius:5px; font-weight:bold; cursor:pointer; }
    .action-btn { padding:5px 10px; margin:2px; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
    .approve { background-color:#4CAF50; color:white; }
    .reject { background-color:#e74c3c; color:white; }
</style>
</head>
<body>

<header>Admin Dashboard</header>

<div class="container">

    <!-- ===== Add New Dog ===== -->
    <h2>Add New Dog</h2>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_dog">
        <input type="text" name="name" placeholder="Dog Name" required>
        <input type="text" name="breed" placeholder="Breed" required>
        <input type="number" name="age" placeholder="Age (years)" required min="0">
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <textarea name="description" placeholder="Description" rows="4" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <select name="status" required>
            <option value="Available">Available</option>
            <option value="Adopted">Adopted</option>
        </select>
        <button type="submit">Add Dog</button>
    </form>

    <!-- ===== All Dogs ===== -->
    <h2>All Dogs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Breed</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($dogs as $dog): ?>
            <tr>
                <td><?php echo $dog['id']; ?></td>
                <td><img src="assets/images/dogs/<?php echo $dog['image']; ?>" alt="<?php echo $dog['name']; ?>" style="width:60px;height:60px;border-radius:5px;"></td>
                <td><?php echo $dog['name']; ?></td>
                <td><?php echo $dog['breed']; ?></td>
                <td><?php echo $dog['age']; ?></td>
                <td><?php echo $dog['gender']; ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="dog_id" value="<?php echo $dog['id']; ?>">
                        <select name="new_status" class="status-select" onchange="this.form.submit()">
                            <option value="Available" <?php if($dog['status']==='Available') echo 'selected'; ?>>Available</option>
                            <option value="Adopted" <?php if($dog['status']==='Adopted') echo 'selected'; ?>>Adopted</option>
                        </select>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ===== Pending Adoption Requests ===== -->
    <h2>Pending Adoption Requests</h2>
    <?php if (count($requests) === 0): ?>
        <p>No pending requests.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Dog</th>
                <th>User</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($requests as $req): ?>
            <tr>
                <td>
                    <img src="assets/images/dogs/<?= htmlspecialchars($req['image']) ?>" width="60"><br>
                    <?= htmlspecialchars($req['dog_name']) ?>
                </td>
                <td><?= htmlspecialchars($req['username']) ?></td>
                <td><?= htmlspecialchars($req['email']) ?></td>
                <td><strong><?= $req['status'] ?></strong></td>
                <td>
                    <form action="process-adoption.php" method="POST" style="display:inline;">
                        <input type="hidden" name="adoption_id" value="<?= $req['adoption_id'] ?>">
                        <input type="hidden" name="dog_id" value="<?= $req['dog_id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="action-btn approve">Approve</button>
                    </form>

                    <form action="process-adoption.php" method="POST" style="display:inline;">
                        <input type="hidden" name="adoption_id" value="<?= $req['adoption_id'] ?>">
                        <input type="hidden" name="dog_id" value="<?= $req['dog_id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="action-btn reject">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>
</body>
</html>
