<?php
session_start();
require 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password']; // No need to trim passwords

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['is_admin'] = $row['is_admin'];
            
            $redirect = $row['is_admin'] ? 'admin-dashboard.php' : 'index.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff9c4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        main {
            max-width: 400px;
            width: 100%;
            background-color: #fffde7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .logo img {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        form input, form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        form button {
            background-color: #ffeb3b;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #fdd835;
        }
    </style>
</head>
<body>
    <main>
        <div class="logo">
            <img src="todo.png" alt="Logo">
        </div>

        <h1>Login</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </main>
</body>
</html>
