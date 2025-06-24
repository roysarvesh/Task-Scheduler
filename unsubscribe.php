<?php
require_once 'functions.php';

$message = '';
$error = '';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    
    if (unsubscribeEmail($email)) {
        $message = 'You have been successfully unsubscribed from task reminder emails.';
    } else {
        $error = 'Email not found in subscribers list or already unsubscribed.';
    }
} else {
    $error = 'Invalid unsubscribe link. Please use the link provided in the email.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - Task Scheduler</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 16px;
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
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .back-link:hover {
            background-color: #0056b3;
        }
        
        .info {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Unsubscribe</h1>
        
        <?php if ($message): ?>
            <div class="message success">
                <strong>Success!</strong><br>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <div class="info">
                You will no longer receive task reminder emails. You can always resubscribe from the main page if you change your mind.
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error">
                <strong>Error!</strong><br>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="back-link">← Back to Task Scheduler</a>
    </div>
</body>
</html>