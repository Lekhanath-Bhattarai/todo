<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');  // Redirect to the homepage if not an admin
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Delete the user from the database
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    // After deletion, redirect back to the admin dashboard
    header('Location: admin-dashboard.php');
    exit;
} else {
    // Redirect if no user ID is provided
    header('Location: admin-dashboard.php');
    exit;
}

$conn->close();
?>
