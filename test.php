<?php
/**
 * Test script for Task Scheduler functionality
 * Run this to verify all functions work correctly
 * Usage: php test.php
 */

require_once 'functions.php';

echo "=== Task Scheduler System Test ===\n\n";

// Test 1: Add tasks
echo "1. Testing addTask() function...\n";
$test_tasks = ['Complete project', 'Buy groceries', 'Call dentist'];
foreach ($test_tasks as $task) {
    $result = addTask($task);
    echo "   Adding '$task': " . ($result ? "SUCCESS" : "FAILED") . "\n";
}

// Test duplicate prevention
echo "   Testing duplicate prevention...\n";
$duplicate_result = addTask('Complete project');
echo "   Adding duplicate 'Complete project': " . ($duplicate_result ? "FAILED (should prevent duplicates)" : "SUCCESS (duplicate prevented)") . "\n";

// Test 2: Get all tasks
echo "\n2. Testing getAllTasks() function...\n";
$tasks = getAllTasks();
echo "   Retrieved " . count($tasks) . " tasks\n";
foreach ($tasks as $task) {
    echo "   - {$task['name']} (ID: {$task['id']}, Completed: " . ($task['completed'] ? 'Yes' : 'No') . ")\n";
}

// Test 3: Mark task as completed
if (!empty($tasks)) {
    echo "\n3. Testing markTaskAsCompleted() function...\n";
    $first_task = $tasks[0];
    $result = markTaskAsCompleted($first_task['id'], true);
    echo "   Marking task '{$first_task['name']}' as completed: " . ($result ? "SUCCESS" : "FAILED") . "\n";
    
    // Verify the change
    $updated_tasks = getAllTasks();
    $updated_task = array_filter($updated_tasks, function($t) use ($first_task) {
        return $t['id'] === $first_task['id'];
    });
    $updated_task = reset($updated_task);
    echo "   Verification - Task is now " . ($updated_task['completed'] ? "completed" : "not completed") . "\n";
}

// Test 4: Email subscription
echo "\n4. Testing email subscription functions...\n";
$test_email = "test@example.com";

// Test verification code generation
$code1 = generateVerificationCode();
$code2 = generateVerificationCode();
echo "   Generated verification codes: $code1, $code2\n";
echo "   Codes are 6 digits: " . (strlen($code1) == 6 && strlen($code2) == 6 ? "SUCCESS" : "FAILED") . "\n";
echo "   Codes are different: " . ($code1 !== $code2 ? "SUCCESS" : "FAILED") . "\n";

// Note: We won't actually send emails in test mode
echo "   Email subscription test (without actual email sending):\n";
echo "   - subscribeEmail() would add email to pending and send verification\n";
echo "   - verifySubscription() would move email from pending to subscribers\n";
echo "   - unsubscribeEmail() would remove email from subscribers\n";

// Test 5: Data file formats
echo "\n5. Testing data file formats...\n";

// Check tasks.txt format
if (file_exists('tasks.txt')) {
    $tasks_content = file_get_contents('tasks.txt');
    $tasks_json = json_decode($tasks_content, true);
    echo "   tasks.txt JSON format: " . (is_array($tasks_json) ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "   tasks.txt: NOT FOUND\n";
}

// Check subscribers.txt format
if (file_exists('subscribers.txt')) {
    $subscribers_content = file_get_contents('subscribers.txt');
    $subscribers_json = json_decode($subscribers_content, true);
    echo "   subscribers.txt JSON format: " . (is_array($subscribers_json) ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "   subscribers.txt: NOT FOUND\n";
}

// Check pending_subscriptions.txt format
if (file_exists('pending_subscriptions.txt')) {
    $pending_content = file_get_contents('pending_subscriptions.txt');
    $pending_json = json_decode($pending_content, true);
    echo "   pending_subscriptions.txt JSON format: " . (is_array($pending_json) || is_object($pending_json) ? "SUCCESS" : "FAILED") . "\n";
} else {
    echo "   pending_subscriptions.txt: NOT FOUND\n";
}

// Test 6: Delete task
if (!empty($tasks) && count($tasks) > 1) {
    echo "\n6. Testing deleteTask() function...\n";
    $last_task = end($tasks);
    $task_count_before = count(getAllTasks());
    $result = deleteTask($last_task['id']);
    $task_count_after = count(getAllTasks());
    
    echo "   Deleting task '{$last_task['name']}': " . ($result ? "SUCCESS" : "FAILED") . "\n";
    echo "   Task count before: $task_count_before, after: $task_count_after\n";
    echo "   Count decreased: " . ($task_count_after < $task_count_before ? "SUCCESS" : "FAILED") . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Check the output above for any FAILED tests.\n";
echo "If all tests show SUCCESS, the system is working correctly.\n\n";

// Display current system state
echo "Current system state:\n";
$final_tasks = getAllTasks();
echo "- Total tasks: " . count($final_tasks) . "\n";
$pending_tasks = array_filter($final_tasks, function($t) { return !$t['completed']; });
echo "- Pending tasks: " . count($pending_tasks) . "\n";
$completed_tasks = array_filter($final_tasks, function($t) { return $t['completed']; });
echo "- Completed tasks: " . count($completed_tasks) . "\n";

$subscribers = getSubscribers();
echo "- Subscribers: " . count($subscribers) . "\n";

$pending_subs = getPendingSubscriptions();
echo "- Pending subscriptions: " . count($pending_subs) . "\n";

echo "\nTo run the web interface, access index.php in your browser.\n";
echo "To set up the CRON job, run: ./setup_cron.sh\n";
?>