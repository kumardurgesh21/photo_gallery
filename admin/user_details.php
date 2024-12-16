<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();
require '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Validate and fetch user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: admin_panel.php');
    exit();
}

$userId = intval($_GET['id']);

// Fetch user details, including last login time
$stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch number of images uploaded by the user
$stmt = $pdo->prepare("SELECT COUNT(*) AS image_count, SUM(LENGTH(image_data)) AS total_image_size FROM images WHERE user_id = ?");
$stmt->execute([$userId]);
$imageData = $stmt->fetch(PDO::FETCH_ASSOC);

$imageCount = $imageData['image_count'];
$totalImageSizeMB = $imageData['total_image_size'] ? round($imageData['total_image_size'] / (1024 * 1024), 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="../assets/css/user_details.css">
</head>
<body>
    <header>
        <h1>User Details</h1>
        <nav>
            <a class="back" href="admin_panel.php">Back to Admin Panel</a>
            <a class="back" href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Details for <?= htmlspecialchars($user['username']); ?></h2>
        <table>
            <tr>
                <th>ID</th>
                <td><?= htmlspecialchars($user['id']); ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?= htmlspecialchars($user['username']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Registered At</th>
                <td><?= htmlspecialchars($user['created_at']); ?></td>
            </tr>
            <tr>
                <th>Last Login</th>
                <td><?= htmlspecialchars($user['last_login'] ?? 'Waiting for data...'); ?></td>
            </tr>
            <tr>
                <th>Number of Images Uploaded</th>
                <td><?= $imageCount; ?></td>
            </tr>
            <tr>
                <th>Total Image Size (MB)</th>
                <td><?= $totalImageSizeMB; ?> MB</td>
            </tr>
        </table>
    </main>
</body>
</html>
