<?php

function addTask($task_name) {
    $task_name = trim($task_name);
    if (empty($task_name)) {
        return false;
    }
    
    $tasks = getAllTasks();
    
    // Check for duplicates
    foreach ($tasks as $task) {
        if (strcasecmp($task['name'], $task_name) === 0) {
            return false; // Duplicate task
        }
    }
    
    $new_task = [
        'id' => uniqid(),
        'name' => $task_name,
        'completed' => false
    ];
    
    $tasks[] = $new_task;
    
    return file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT)) !== false;
}

function getAllTasks() {
    if (!file_exists('tasks.txt')) {
        file_put_contents('tasks.txt', '[]');
        return [];
    }
    
    $content = file_get_contents('tasks.txt');
    $tasks = json_decode($content, true);
    
    return is_array($tasks) ? $tasks : [];
}

function markTaskAsCompleted($task_id, $is_completed) {
    $tasks = getAllTasks();
    
    foreach ($tasks as &$task) {
        if ($task['id'] === $task_id) {
            $task['completed'] = (bool)$is_completed;
            file_put_contents('tasks.txt', json_encode($tasks, JSON_PRETTY_PRINT));
            return true;
        }
    }
    
    return false;
}

function deleteTask($task_id) {
    $tasks = getAllTasks();
    $original_count = count($tasks);
    
    $tasks = array_filter($tasks, function($task) use ($task_id) {
        return $task['id'] !== $task_id;
    });
    
    if (count($tasks) < $original_count) {
        file_put_contents('tasks.txt', json_encode(array_values($tasks), JSON_PRETTY_PRINT));
        return true;
    }
    
    return false;
}

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function subscribeEmail($email) {
    $email = trim(strtolower($email));
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    // Check if already subscribed
    if (isEmailSubscribed($email)) {
        return false;
    }
    
    $code = generateVerificationCode();
    $pending = getPendingSubscriptions();
    
    $pending[$email] = [
        'code' => $code,
        'timestamp' => time()
    ];
    
    file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));
    
    // Send verification email
    $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/verify.php?email=" . urlencode($email) . "&code=" . $code;
    
    $subject = "Verify subscription to Task Planner";
    $body = '<p>Click the link below to verify your subscription to Task Planner:</p>
<p><a id="verification-link" href="' . $verification_link . '">Verify Subscription</a></p>';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    
    return mail($email, $subject, $body, $headers);
}

function verifySubscription($email, $code) {
    $email = trim(strtolower($email));
    $pending = getPendingSubscriptions();
    
    if (!isset($pending[$email]) || $pending[$email]['code'] !== $code) {
        return false;
    }
    
    // Move from pending to subscribers
    $subscribers = getSubscribers();
    if (!in_array($email, $subscribers)) {
        $subscribers[] = $email;
        file_put_contents('subscribers.txt', json_encode($subscribers, JSON_PRETTY_PRINT));
    }
    
    // Remove from pending
    unset($pending[$email]);
    file_put_contents('pending_subscriptions.txt', json_encode($pending, JSON_PRETTY_PRINT));
    
    return true;
}

function unsubscribeEmail($email) {
    $email = trim(strtolower($email));
    $subscribers = getSubscribers();
    
    $key = array_search($email, $subscribers);
    if ($key !== false) {
        unset($subscribers[$key]);
        file_put_contents('subscribers.txt', json_encode(array_values($subscribers), JSON_PRETTY_PRINT));
        return true;
    }
    
    return false;
}

function sendTaskReminders() {
    $subscribers = getSubscribers();
    $tasks = getAllTasks();
    
    $pending_tasks = array_filter($tasks, function($task) {
        return !$task['completed'];
    });
    
    if (empty($pending_tasks)) {
        return true; // No pending tasks to send
    }
    
    $success = true;
    foreach ($subscribers as $email) {
        if (!sendTaskEmail($email, $pending_tasks)) {
            $success = false;
        }
    }
    
    return $success;
}

function sendTaskEmail($email, $pending_tasks) {
    $unsubscribe_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/unsubscribe.php?email=" . urlencode($email);
    
    $subject = "Task Planner - Pending Tasks Reminder";
    
    $body = '<h2>Pending Tasks Reminder</h2>
<p>Here are the current pending tasks:</p>
<ul>';
    
    foreach ($pending_tasks as $task) {
        $body .= '<li>' . htmlspecialchars($task['name']) . '</li>';
    }
    
    $body .= '</ul>
<p><a id="unsubscribe-link" href="' . $unsubscribe_link . '">Unsubscribe from notifications</a></p>';
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";
    
    return mail($email, $subject, $body, $headers);
}

// Helper functions
function getSubscribers() {
    if (!file_exists('subscribers.txt')) {
        file_put_contents('subscribers.txt', '[]');
        return [];
    }
    
    $content = file_get_contents('subscribers.txt');
    $subscribers = json_decode($content, true);
    
    return is_array($subscribers) ? $subscribers : [];
}

function getPendingSubscriptions() {
    if (!file_exists('pending_subscriptions.txt')) {
        file_put_contents('pending_subscriptions.txt', '{}');
        return [];
    }
    
    $content = file_get_contents('pending_subscriptions.txt');
    $pending = json_decode($content, true);
    
    return is_array($pending) ? $pending : [];
}

function isEmailSubscribed($email) {
    $subscribers = getSubscribers();
    return in_array(trim(strtolower($email)), $subscribers);
}

?>