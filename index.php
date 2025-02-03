<?php
session_start();
  // Stop execution and see session data

include 'check_session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

require_once 'db.php';

if (isset($_POST['add_task'])) {
    $description = $_POST['description'];
    $category = $_POST['category'];
    $due_date = $_POST['due_date'];

    if (!empty($description) && !empty($category) && !empty($due_date)) {
        $stmt = $conn->prepare("INSERT INTO tasks (description, category, due_date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $description, $category, $due_date, $_SESSION['user_id']);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_GET['mark_complete'])) {
    $task_id = $_GET['mark_complete'];
    $stmt = $conn->prepare("UPDATE tasks SET completed = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $task_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param('ii', $task_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);

function calculateDateRemaining($due_date) {
    $current_date = new DateTime();
    $due_date = new DateTime($due_date);
    
    $interval = $current_date->diff($due_date);
    
    return $interval->format('%r%a');
}

function formatDate($due_date) {
    $date = new DateTime($due_date);
    return $date->format('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome, <?php echo htmlspecialchars($username); ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fff9c4; 
            margin: 0;
            padding: 0;
        }
        
        /* Header Style */
        header {
            background-color: #ffeb3b; 
            color: #333;
            text-align: center;
            padding: 20px;
            font-size: 24px;
            border-radius: 10px 10px 0 0;
        }
        
        header a {
            color: #333; 
            text-decoration: none;
            padding: 10px 20px;
            background-color: #fdd835;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        header a:hover {
            background-color: #ffeb3b;
        }
        
        main {
            max-width: 900px;
            margin: 20px auto;
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

        form input, form button {
            display: block;
            width: 100%;
            margin: 12px 0;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
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

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }
        
        th {
            background-color: #fffde7; 
        }
        
        tr.completed {
            background-color: #f0f4c3; 
        }
        
        tr:hover {
            background-color: #fff59d; 
        }
      
        a.complete {
            padding: 8px 15px;
            background-color: #4CAF50; 
            color: white;
            text-decoration: none;
            margin-right: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a.delete {
            padding: 8px 15px;
            background-color: #f44336; 
            color: white;
            text-decoration: none;
            margin-right: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        a.complete:hover {
            background-color: #388E3C; 
        }

        a.delete:hover {
            background-color: #d32f2f; 
        }

        .overdue {
            color: #f44336; 
        }
        
        .due-today {
            color: #FF9800; 
        }
        
        .in-future {
            color: #4CAF50; 
        }
        
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
    <h1>Welcome user</h1>
        <a href="logout.php">Logout</a>
    </header>

    <main>
        <h2>Your Dashboard</h2>

        <form method="POST" action="">
            <h3>Add New Task</h3>
            <input type="text" name="description" placeholder="Task Description" required>
            <input type="text" name="category" placeholder="Category" required>
            <input type="date" name="due_date" required>
            <button type="submit" name="add_task">Add Task</button>
        </form>

        <table>
            <tr>
                <th>Task</th>
                <th>Category</th>
                <th>Due Date</th>
                <th>Date Remaining</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($tasks as $task): ?>
                <?php
                $remaining_days = calculateDateRemaining($task['due_date']);
                $status = $task['completed'] ? 'Completed' : 'Incomplete';

                if ($remaining_days < 0) {
                    $status_class = 'overdue';
                } elseif ($remaining_days == 0) {
                    $status_class = 'due-today';
                } else {
                    $status_class = 'in-future';
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($task['description']); ?></td>
                    <td><?php echo htmlspecialchars($task['category']); ?></td>
                    <td><?php echo formatDate($task['due_date']); ?></td>
                    <td class="<?php echo $status_class; ?>">
                        <?php
                            if ($remaining_days < 0) {
                                echo "Overdue by " . abs($remaining_days) . " days";
                            } elseif ($remaining_days == 0) {
                                echo "Due today";
                            } else {
                                echo "In " . $remaining_days . " days";
                            }
                        ?>
                    </td>
                    <td><?php echo $status; ?></td>
                    <td>
                        <?php if (!$task['completed']): ?>
                            <a href="index.php?mark_complete=<?php echo $task['id']; ?>" class="complete">Mark Complete</a>
                        <?php else: ?>
                            <span class="completed">Completed</span>
                        <?php endif; ?>
                        <a href="index.php?delete=<?php echo $task['id']; ?>" class="delete">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>
