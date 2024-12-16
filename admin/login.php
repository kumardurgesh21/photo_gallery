<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

// Define static credentials
define('ADMIN_EMAIL', 'suportkumar@gmail.com');
define('ADMIN_PASSWORD', '123456'); // Use a strong password

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verify the static email and password
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = ADMIN_EMAIL;
        header('Location: admin_panel.php'); // Redirect to admin panel
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        <form action="login.php" method="POST">
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            
            <button type="submit">Login</button>
        </form>
        <p>Not registered? Contact support for admin access.</p>
        <p> Password Hint: static pass</p>
    </div>
</body>
</html>
