<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Photo Gallery</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <header>
        <h1>Welcome to Photo Gallery</h1>
    </header>

    <main>
        <div class="welcome-message">
            <h2>Create an Account to Make Your Image Gallery</h2>
            <p>Join us to upload and manage your images in a personalized gallery. It's quick and easy!</p>
            <div class="buttons">
                <a href="login.php" class="btn">Login</a>
                <a href="register.php" class="btn">Register</a>
            </div>
        </div>
    </main>
</body>
</html>
