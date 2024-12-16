<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST['input']); // Can be username or email
    $password = trim($_POST['password']);

    // Check for user in the database using username OR email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$input, $input]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store user information in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: gallery.php'); // Redirect to gallery
        exit();
    } else {
        $error = "Invalid username/email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Photo Gallery</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <header>
        <h1>Login</h1>
    </header>
    <main>
        <form action="login.php" method="POST">
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <label for="input">Username or Email:</label>
            <input type="text" name="input" id="input" required> <!-- Accepts both username and email -->

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Login</button>
        </form>

        <p>Not registered yet? <a href="register.php">Create an account</a>.</p>
        <!-- <p><a href="forgot_password.php">Forgot Password?</a></p> -->

    </main>
</body>
</html>
