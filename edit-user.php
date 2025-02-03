<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        header('Location: admin-dashboard.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $is_admin = isset($_POST['is_admin']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
        $stmt->bind_param('ii', $is_admin, $user_id);
        $stmt->execute();

        header('Location: admin-dashboard.php');
        exit;
    }

    $stmt->close();
} else {
    header('Location: admin-dashboard.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit User</h1>
    </header>

    <main>
        <div class="container">
            <form action="edit-user.php?id=<?php echo htmlspecialchars($user['id']); ?>" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>

                <label for="is_admin">Is Admin:</label>
                <input type="checkbox" name="is_admin" id="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>>

                <button type="submit">Update User</button>
            </form>

            <p><a href="admin-dashboard.php">Back to Admin Dashboard</a></p>
        </div>
    </main>
</body>
</html>
