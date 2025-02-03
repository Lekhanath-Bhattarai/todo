<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $category = htmlspecialchars($_POST['category']);
    $userId = 1; //

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, category) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $userId, $title, $description, $category);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}
?>
