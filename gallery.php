<?php

error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Show errors on screen
ini_set('display_startup_errors', 1); // Show startup errors

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Handle image upload only when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['image'])) {
    $imageTitle = trim($_POST['title']);
    $image = $_FILES['image'];

    // Validate file
    if ($image['size'] > 5000000) {
        $_SESSION['error'] = "File too large. Max 5MB allowed.";
    } elseif (!in_array($image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $_SESSION['error'] = "Only JPG, PNG, and GIF formats are allowed.";
    } else {
        // Read image binary data
        $imageData = file_get_contents($image['tmp_name']);
        $mimeType = $image['type'];

        try {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO images (user_id, title, image_data, mime_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $imageTitle, $imageData, $mimeType]);
            
            // Set success message in session
            $_SESSION['success'] = "Image uploaded successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
    }

    // Redirect to prevent resubmission of form after page refresh
    header("Location: gallery.php");
    exit();
}

// Fetch images from the database
$stmt = $pdo->prepare("SELECT id, title, image_data, mime_type FROM images WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Gallery</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .alert {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            margin-bottom: 15px;
            position: relative;
            border-radius: 5px;
        }
        .alert .closebtn {
            position: absolute;
            top: 0;
            right: 10px;
            color: white;
            font-size: 20px;
            cursor: pointer;
        }
        .gallery-item {
            position: relative;
            display: inline-block;
            margin: 10px;
        }
        .gallery-item img {
            max-width: 200px;
            height: auto;
            display: block;
        }
        .btn-container {
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 12px;
            margin: 5px;
        }
        .btn-download {
            background-color: #008CBA;
        }
        .btn-delete {
            background-color: #f44336;
        }
        .btn:hover {
            opacity: 0.8;
        }

        /* Full-screen confirmation modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent background */
            color: white;
            text-align: center;
            padding-top: 100px;
        }

        .modal-content {
            background-color: #f44336; /* Red background */
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            max-width: 400px;
            margin: 0 auto;
        }

        .modal-content h2 {
            margin-bottom: 20px;
        }

        .modal-buttons {
            margin-top: 20px;
        }

        .modal-buttons a {
            padding: 10px 20px;
            margin: 5px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .cancel {
            background-color: #4CAF50;
        }

        .confirm {
            background-color: #f44336;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?></h1>
    <a href="logout.php">Logout</a>

    <h2>Upload a New Image</h2>
    <form action="gallery.php" method="POST" enctype="multipart/form-data">
        <label for="title">Image Title:</label>
        <input type="text" name="title" id="title" required>
        <label for="image">Choose an Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Upload Image</button>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </form>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION['success']); ?>
            <span class="closebtn" onclick="this.parentElement.style.display='none'; 
                                            <?php unset($_SESSION['success']); ?>">&times;</span>
        </div>
    <?php unset($_SESSION['success']); endif; ?>

    <h2>Your Gallery</h2>
    <div class="gallery">
    <?php if ($images): ?>
    <?php foreach ($images as $image): ?>
        <div class="gallery-item">
            <p><?= htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            <img src="data:<?= htmlspecialchars($image['mime_type'], ENT_QUOTES, 'UTF-8'); ?>;base64,<?= base64_encode($image['image_data']); ?>" 
                 alt="<?= htmlspecialchars($image['title'], ENT_QUOTES, 'UTF-8'); ?>">

            <!-- Buttons below the image -->
            <div class="btn-container">
                <!-- Download Button -->
                <a href="data:<?= htmlspecialchars($image['mime_type']); ?>;base64,<?= base64_encode($image['image_data']); ?>" 
                   download="<?= htmlspecialchars($image['title']); ?>" class="btn btn-download">Download</a>

                <!-- Link to trigger the full-screen modal -->
                <a href="gallery.php?confirm_delete=<?= $image['id']; ?>" class="btn btn-delete">Delete</a>
                <!-- <p><strong>Uploaded on:</strong> <?= htmlspecialchars(date("F j, Y, g:i a", strtotime($image['uploaded_at']))); ?></p> -->
            </div>
        </div>
    <?php endforeach; ?>
    <?php else: ?>
        <p>No images uploaded yet.</p>
    <?php endif; ?>
    </div>

    <!-- Full-Screen Modal for Delete Confirmation -->
    <?php if (isset($_GET['confirm_delete'])): ?>
    <div class="modal" style="display: block;">
        <div class="modal-content">
            <h2>Are you sure you want to delete this image?</h2>
            <div class="modal-buttons">
                <a href="delete_image.php?id=<?= $_GET['confirm_delete']; ?>" class="confirm">Yes, Delete</a>
                <a href="gallery.php" class="cancel">Cancel</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
