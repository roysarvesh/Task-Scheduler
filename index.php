<?php
require_once 'functions.php';

$message = '';
$error = '';

// Handle task operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name']) && !empty($_POST['task-name'])) {
        if (addTask($_POST['task-name'])) {
            $message = 'Task added successfully!';
        } else {
            $error = 'Failed to add task or task already exists.';
        }
    } elseif (isset($_POST['email']) && !empty($_POST['email'])) {
        if (subscribeEmail($_POST['email'])) {
            $message = 'Verification email sent! Please check your inbox.';
        } else {
            $error = 'Failed to subscribe. Email may already be subscribed or invalid.';
        }
    } elseif (isset($_POST['mark_complete'])) {
        $task_id = $_POST['task_id'];
        $is_completed = isset($_POST['completed']) ? true : false;
        if (markTaskAsCompleted($task_id, $is_completed)) {
            $message = 'Task status updated!';
        } else {
            $error = 'Failed to update task status.';
        }
    } elseif (isset($_POST['delete_task'])) {
        $task_id = $_POST['task_id'];
        if (deleteTask($task_id)) {
            $message = 'Task deleted successfully!';
        } else {
            $error = 'Failed to delete task.';
        }
    }
}

$tasks = getAllTasks();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        h1, h2 {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        button:hover {
            background-color: #0056b3;
        }
        
        .delete-task {
            background-color: #dc3545;
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .delete-task:hover {
            background-color: #c82333;
        }
        
        .tasks-list {
            list-style: none;
            padding: 0;
        }
        
        .task-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin-bottom: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }
        
        .task-item.completed {
            background-color: #d4edda;
            text-decoration: line-through;
            opacity: 0.7;
        }
        
        .task-status {
            margin-right: 10px;
        }
        
        .task-name {
            flex-grow: 1;
            margin-right: 10px;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .task-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .task-form input {
            flex-grow: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Task Scheduler</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <h2>Add New Task</h2>
        <form method="POST" class="task-form">
            <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
            <button type="submit" id="add-task">Add Task</button>
        </form>
    </div>
    
    <div class="container">
        <h2>Tasks</h2>
        <?php if (empty($tasks)): ?>
            <p>No tasks found. Add a task above to get started!</p>
        <?php else: ?>
            <ul class="tasks-list">
                <?php foreach ($tasks as $task): ?>
                    <li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <input type="checkbox" 
                                   class="task-status" 
                                   name="completed" 
                                   <?php echo $task['completed'] ? 'checked' : ''; ?>
                                   onchange="this.form.submit()">
                            <input type="hidden" name="mark_complete" value="1">
                        </form>
                        
                        <span class="task-name"><?php echo htmlspecialchars($task['name']); ?></span>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <button type="submit" name="delete_task" class="delete-task" 
                                    onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="container">
        <h2>Email Subscription</h2>
        <p>Subscribe to receive hourly email reminders for pending tasks.</p>
        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Enter your email address" required>
            </div>
            <button type="submit" id="submit-email">Subscribe</button>
        </form>
    </div>
</body>
</html>