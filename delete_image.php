<?php
session_start();
require 'config.php';

if (isset($_GET['id'])) {
    $imageId = (int) $_GET['id'];

    // Prepare delete statement
    $stmt = $pdo->prepare("DELETE FROM images WHERE id = ? AND user_id = ?");
    $stmt->execute([$imageId, $_SESSION['user_id']]);

    // Set a success message and redirect back to gallery
    $_SESSION['success'] = "Image deleted successfully!";
    header('Location: gallery.php');
    exit();
} else {
    // If no image ID is provided, redirect to the gallery
    $_SESSION['error'] = "Image not found!";
    header('Location: gallery.php');
    exit();
}
