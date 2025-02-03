<?php
include 'db.php';

if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param('i', $taskId);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit();
}

$conn->close();
?>
