<?php
// This script is designed to be run by CRON every hour
// It sends task reminder emails to all subscribed users

require_once 'functions.php';

// Log file for cron execution tracking
$log_file = 'cron_log.txt';

function logMessage($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$message}\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Start of cron execution
logMessage("CRON job started");

try {
    // Send task reminders
    $result = sendTaskReminders();
    
    if ($result) {
        logMessage("Task reminders sent successfully");
    } else {
        logMessage("Some reminders failed to send");
    }
    
    // Get some stats for logging
    $subscribers = getSubscribers();
    $tasks = getAllTasks();
    $pending_tasks = array_filter($tasks, function($task) {
        return !$task['completed'];
    });
    
    logMessage("Statistics - Subscribers: " . count($subscribers) . ", Total tasks: " . count($tasks) . ", Pending tasks: " . count($pending_tasks));
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
}

logMessage("CRON job completed\n");

// If running from command line, output the result
if (php_sapi_name() === 'cli') {
    echo "Task reminder cron job completed. Check cron_log.txt for details.\n";
}
?>