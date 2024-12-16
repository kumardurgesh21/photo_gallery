<?php

error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Show errors on screen
ini_set('display_startup_errors', 1); // Show startup errors

session_start();
require '../config.php'; // Ensure the correct path to config.php

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

try {
    // Fetch users
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage()); // Display a message if the query fails
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        /* Reset some basic styles */
        body, h1, h2, table {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #a91212;
            line-height: 1.6;
        }

        header {
            background-color: #bb0202;
            color: #fff;
            padding: 1em 0;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin: 0 1em;
        }

        main {
            padding: 2em;
        }

        h2 {
            text-align: center;
            margin-bottom: 1em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2em;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 1em;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Button Styling */
        button, a.button {
            display: inline-block;
            padding: 0.5em 1.2em;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* View Details Button */
        a.button.details {
            background-color: #3498db;
        }

        a.button.details:hover {
            background-color: #2980b9;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.6);
        }

        /* Delete Button */
        a.button.delete {
            background-color: #e74c3c;
        }

        a.button.delete:hover {
            background-color: #c0392b;
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.6);
        }

        /* Logout Button */
        a.button.logout {
            background-color: #2ecc71; /* Green */
            padding: 0.1em 0.6em;
            font-size: 1em;
            margin-left: 1em;
        }

        a.button.logout:hover {
            background-color: #27ae60; /* Darker Green */
            box-shadow: 0 4px 8px rgba(46, 204, 113, 0.6);
        }

        /* Centered and Spaced Actions */
        td > a.button {
            margin-right: 0.5em;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <a href="logout.php" class="button logout">Logout</a> <!-- Logout button with unique style -->
        </nav>
    </header>
    <main>
        <h2>Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= $user['verified'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="delete_user.php?id=<?= htmlspecialchars($user['id']); ?>" class="button delete">Delete</a>
                                <a href="user_details.php?id=<?= htmlspecialchars($user['id']); ?>" class="button details">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
