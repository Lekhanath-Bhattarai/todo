<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
    $stmt->bind_param('ii', $is_admin, $user_id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin-dashboard.php');
    exit;
}

if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin-dashboard.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff9c4;
            margin: 0;
            padding: 0;
        }
        
        header {
            background-color: #ffeb3b;
            color: #333;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header a {
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #fdd835;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 0 10px;
        }

        header a:hover {
            background-color: #ffeb3b;
        }
        
        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fffde7;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            text-align: center;
            color: #333;
        }

        h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .card {
            background-color: #fffef5;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .card h3 {
            margin-top: 0;
        }

        .card form {
            display: flex;
            flex-direction: column;
        }

        .card form input, .card form button {
            margin: 10px 0;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .card form input:focus, .card form button:focus {
            outline: none;
            border-color: #ffeb3b;
        }

        .card form button {
            background-color: #ffeb3b;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .card form button:hover {
            background-color: #fdd835;
        }

        .user-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .user-card {
            background-color: #fffef5;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33.333% - 20px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .user-card h4 {
            margin-top: 0;
        }

        .user-card .actions {
            display: flex;
            justify-content: space-between;
        }

        .user-card a {
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .user-card a.edit {
            background-color: #4CAF50;
            color: white;
        }

        .user-card a.delete {
            background-color: #f44336;
            color: white;
        }

        .user-card a.edit:hover {
            background-color: #388E3C;
        }

        .user-card a.delete:hover {
            background-color: #d32f2f;
        }
    </style>
    <script>
        function openEditForm(userId, isAdmin) {
            document.getElementById('edit_user_id').value = userId;
            document.getElementById('edit_is_admin').checked = isAdmin;
            document.getElementById('edit_form').style.display = 'block';
        }
    </script>
</head>
<body>
    <header>
        <div>
            <a href="admin-dashboard.php">Admin Dashboard</a>
            <a href="add-user.php">Add New User</a>
        </div>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <h2>User Management</h2>

        <div class="card" id="edit_form" style="display: none;">
            <h3>Edit User</h3>
            <form action="admin-dashboard.php" method="POST">
                <input type="hidden" name="user_id" id="edit_user_id">
                <label for="edit_is_admin">Admin:</label>
                <input type="checkbox" name="is_admin" id="edit_is_admin">
                <button type="submit" name="edit_user">Save Changes</button>
            </form>
        </div>

        <div class="user-list">
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p>Admin: <?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></p>
                    <div class="actions">
                        <a href="javascript:void(0);" onclick="openEditForm(<?php echo $user['id']; ?>, <?php echo $user['is_admin'] ? 'true' : 'false'; ?>);" class="edit">Edit</a>
                        <a href="admin-dashboard.php?delete=<?php echo $user['id']; ?>" class="delete">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
