<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $username, $password, $is_admin);
    $stmt->execute();
    $stmt->close();

    header('Location: login.php');
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff9c4; 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        
        header {
            background-color: #ffeb3b; 
            color: #333; 
            text-align: center;
            padding: 20px;
            font-size: 24px;
            border-radius: 10px 10px 0 0;
        }
        
        main {
            width: 100%;
            max-width: 400px;
            margin: 0;
            padding: 0;
            background-color: #fffde7; 
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .container {
            padding: 20px;
        }
        
        form {
            display: flex;
            flex-direction: column;
        }
        
        form label {
            margin-top: 10px;
        }

        form input, form button {
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }
        
        form input:focus, form button:focus {
            outline: none;
            border-color: #ffeb3b;
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
        
        p a {
            color: #333; 
            text-decoration: none;
        }
    </style>
    <script>
        function validateForm() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            if (username === '' || password === '') {
                alert('Please fill out all fields.');
                return false;
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters long.');
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <main>
        <header>
            <h1>Register</h1>
        </header>

        <div class="container">
            <form action="register.php" method="POST" onsubmit="return validateForm();">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </main>
</body>
</html>
