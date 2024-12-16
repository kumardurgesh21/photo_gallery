<?php
require 'config.php';  // Database connection setup

// Ensure the image ID is provided
if (isset($_GET['id'])) {
    $imageId = intval($_GET['id']);  // Sanitize the ID

    // Prepare and execute the query to fetch the image
    $stmt = $pdo->prepare("SELECT image_data, mime_type FROM images WHERE id = ?");
    $stmt->execute([$imageId]);

    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    // If image is found, output it
    if ($image) {
        header("Content-Type: " . $image['mime_type']);  // Set the appropriate MIME type
        echo $image['image_data'];  // Output the image binary data
    } else {
        // Handle image not found
        header("HTTP/1.0 404 Not Found");
        echo "Image not found.";
    }
} else {
    // Handle missing ID
    echo "No image ID provided.";
}
?>
