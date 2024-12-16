<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE otp = ?");
    $stmt->execute([$otp]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET verified = 1 WHERE otp = ?");
        $stmt->execute([$otp]);
        echo "Email verified!";
    } else {
        echo "Invalid OTP!";
    }
}
?>
