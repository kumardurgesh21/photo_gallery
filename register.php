<?php
error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Show errors on screen
ini_set('display_startup_errors', 1); // Show startup errors

session_start();
require 'config.php'; // Database configuration

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Check if the email is already registered
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                // Insert user into the database (without OTP)
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verified) VALUES (?, ?, ?, 1)");
                $stmt->execute([$username, $email, $hashedPassword]);

                // Redirect to login page after successful registration
                echo "<script>
                alert('Registration successful! Please log in.');
                window.location.href = 'login.php';
              </script>";
                exit();
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Photo Gallery</title>
    <link rel="stylesheet" href="assets/css/register.css"> <!-- Add your CSS file -->
</head>
<body>
    <header>
        <h1>Register</h1>
    </header>
    <main>
        <form action="register.php" method="POST">
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Register</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login now</a>.</p>

    </main>
</body>
</html>
