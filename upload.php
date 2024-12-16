<?php
session_start();
require 'config.php';

if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['image'];

    $filePath = 'uploads/' . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $stmt = $pdo->prepare("INSERT INTO images (user_id, file_path) VALUES (?, ?)");
        $stmt->execute([$user_id, $filePath]);
        echo "Image uploaded successfully!";
    } else {
        echo "Failed to upload image.";
    }
}
?>
